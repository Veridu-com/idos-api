<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

use App\Command;
use App\Exception\AppException;
use App\Factory;
use App\Handler;
use App\Helper;
use App\Middleware;
use App\Middleware\Auth;
use App\Middleware\TransactionMiddleware;
use App\Repository;
use Aws\S3\S3Client;
use GuzzleHttp\Client as HttpClient;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Connection;
use Interop\Container\ContainerInterface;
use Jenssegers\Optimus\Optimus;
use Lcobucci\JWT;
use League\Event\Emitter;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Adapter\NullAdapter;
use League\Flysystem\AdapterInterface;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Cached\CachedAdapter;
use League\Flysystem\Cached\Storage\Stash as Cache;
use League\Flysystem\Filesystem;
use League\Flysystem\Plugin\ListFiles;
use League\Tactician\CommandBus;
use League\Tactician\Container\ContainerLocator;
use League\Tactician\Handler\CommandHandlerMiddleware;
use League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor;
use League\Tactician\Handler\MethodNameInflector\HandleClassNameInflector;
use League\Tactician\Logger\Formatter\ClassNameFormatter;
use League\Tactician\Logger\Formatter\ClassPropertiesFormatter;
use League\Tactician\Logger\LoggerMiddleware;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Monolog\Processor\WebProcessor;
use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Storage\Memory;
use OAuth\ServiceFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Validator;
use Slim\HttpCache\CacheProvider;
use Stash\Driver\Apc;
use Stash\Driver\Composite;
use Stash\Driver\Ephemeral;
use Stash\Driver\FileSystem as FileSystemCache;
use Stash\Driver\Memcache;
use Stash\Driver\Redis;
use Stash\Driver\Sqlite;
use Stash\Pool;
use Whoops\Handler\PrettyPageHandler;

if (! isset($app)) {
    die('$app is not set!');
}

$container = $app->getContainer();

// Slim Error Handling
$container['errorHandler'] = function (ContainerInterface $container) : callable {
    return function (
        ServerRequestInterface $request,
        ResponseInterface $response,
        \Throwable $exception
    ) use ($container) : ResponseInterface {
        $settings = $container->get('settings');
        $response = $container
            ->get('httpCache')
            ->denyCache($response);

        $log = $container->get('log');
        $log('Foundation')->error(
            sprintf(
                '%s [%s:%d]',
                $exception->getMessage(),
                $exception->getFile(),
                $exception->getLine()
            )
        );
        $log('Foundation')->debug($exception->getTraceAsString());

        $previousException = $exception->getPrevious();
        if ($previousException) {
            $log('Foundation')->error(
                sprintf(
                    '%s [%s:%d]',
                    $previousException->getMessage(),
                    $previousException->getFile(),
                    $previousException->getLine()
                )
            );
            $log('Foundation')->debug($previousException->getTraceAsString());
        }

        if ($exception instanceof AppException) {
            $log('API')->info(
                sprintf(
                    '%s [%s:%d]',
                    $exception->getMessage(),
                    $exception->getFile(),
                    $exception->getLine()
                )
            );
            $log('API')->debug($exception->getTraceAsString());

            $body = [
                'status' => false,
                'error'  => [
                    'id'      => $container->get('logUidProcessor')->getUid(),
                    'code'    => $exception->getCode(),
                    'type'    => 'APPLICATION_EXCEPTION', // $exception->getType(),
                    'link'    => 'https://docs.idos.io/errors/APPLICATION_EXCEPTION', // $exception->getLink(),
                    'message' => $exception->getMessage(),
                ]
            ];

            if ($settings['debug']) {
                $body['error']['trace'] = $exception->getTrace();
            }

            $command = $container
                ->get('commandFactory')
                ->create('ResponseDispatch');
            $command
                ->setParameter('request', $request)
                ->setParameter('response', $response)
                ->setParameter('body', $body)
                ->setParameter('statusCode', $exception->getCode());

            return $container->get('commandBus')->handle($command);
        }

        if ($settings['debug']) {
            $prettyPageHandler = new PrettyPageHandler();
            // Add more information to the PrettyPageHandler
            $prettyPageHandler->addDataTable(
                'Request',
                [
                    'Accept Charset'  => $request->getHeader('ACCEPT_CHARSET') ?: '<none>',
                    'Content Charset' => $request->getContentCharset() ?: '<none>',
                    'Path'            => $request->getUri()->getPath(),
                    'Query String'    => $request->getUri()->getQuery() ?: '<none>',
                    'HTTP Method'     => $request->getMethod(),
                    'Base URL'        => (string) $request->getUri(),
                    'Scheme'          => $request->getUri()->getScheme(),
                    'Port'            => $request->getUri()->getPort(),
                    'Host'            => $request->getUri()->getHost()
                ]
            );

            $whoops = new Whoops\Run();
            $whoops->pushHandler($prettyPageHandler);

            return $response
                ->withStatus(500)
                ->write($whoops->handleException($exception));
        }

        $body = [
            'status' => false,
            'error'  => [
                'id'      => $container->get('logUidProcessor')->getUid(),
                'code'    => 500,
                'type'    => 'APPLICATION_ERROR',
                'link'    => 'https://docs.idos.io/errors/APPLICATION_ERROR',
                'message' => 'Internal Application Error'
            ]
        ];

        $command = $container->get('commandFactory')->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body)
            ->setParameter('statusCode', 500);

        return $container->get('commandBus')->handle($command);
    };
};

// PHP Error Handler
$container['phpErrorHandler'] = function (ContainerInterface $container) : callable {
    return $container->errorHandler;
};

// Slim Not Found Handler
$container['notFoundHandler'] = function (ContainerInterface $container) : callable {
    return function (
        ServerRequestInterface $request,
        ResponseInterface $response
    ) use ($container) : ResponseInterface {
        throw new AppException('Whoopsies! Route not found!', 404);
    };
};

// Slim Not Allowed Handler
$container['notAllowedHandler'] = function (ContainerInterface $container) : callable {
    return function (
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $methods
    ) use ($container) : ResponseInterface {
        if ($request->isOptions()) {
            return $response->withStatus(204);
        }

        throw new AppException('Whoopsies! Method not allowed for this route!', 400);
    };
};

// Monolog Request UID Processor
$container['logUidProcessor'] = function (ContainerInterface $container) : callable {
    return new UidProcessor();
};

// Monolog Request Processor
$container['logWebProcessor'] = function (ContainerInterface $container) : callable {
    return new WebProcessor();
};

// Monolog Logger
$container['log'] = function (ContainerInterface $container) : callable {
    return function ($channel = 'API') use ($container) : Logger {
        $settings = $container->get('settings');
        $logger   = new Logger($channel);
        $logger
            ->pushProcessor($container->get('logUidProcessor'))
            ->pushProcessor($container->get('logWebProcessor'))
            ->pushHandler(new StreamHandler($settings['log']['path'], $settings['log']['level']));

        return $logger;
    };
};

// Stash Cache
$container['cache'] = function (ContainerInterface $container) : Pool {
    $settings = $container->get('settings');
    if (empty($settings['cache']['driver'])) {
        throw new \RuntimeException('cache:driver is not set');
    }

    switch ($settings['cache']['driver']) {
        case 'filesystem':
            $driver = new FileSystemCache($settings['cache']['options'] ?? []);
            break;
        case 'sqlite':
            $driver = new Sqlite($settings['cache']['options'] ?? []);
            break;
        case 'apc':
            $driver = new Apc($settings['cache']['options'] ?? []);
            break;
        case 'memcache':
            $driver = new Memcache($settings['cache']['options'] ?? []);
            break;
        case 'redis':
            $driver = new Redis($settings['cache']['options'] ?? []);
            break;
        case 'ephemeral':
            $driver = new Ephemeral();
            break;
        default:
            throw new \RuntimeException('Invalid Cache driver');
    }

    if (! $driver instanceof Ephemeral) {
        $driver = new Composite(
            [
                'drivers' => [
                    new Ephemeral(),
                    $driver
                ]
            ]
        );
    }

    $logger = $container->get('log');
    $pool   = new Pool($driver);
    $pool->setLogger($logger('Cache'));
    $pool->setNamespace('API');

    return $pool;
};

// Slim HTTP Cache
$container['httpCache'] = function (ContainerInterface $container) : CacheProvider {
    return new CacheProvider();
};

// Tactician Command Bus
$container['commandBus'] = function (ContainerInterface $container) : CommandBus {
    $settings = $container->get('settings');
    $log      = $container->get('log');

    $commandList = [];
    if ((! empty($settings['boot']['commandsCache'])) && (is_file($settings['boot']['commandsCache']))) {
        $cache = file_get_contents($settings['boot']['commandsCache']);
        if ($cache !== false) {
            $cache = unserialize($cache);
        }

        if ($cache !== false) {
            $commandList = $cache;
        }
    }

    if (empty($commandList)) {
        $commandFiles = new RegexIterator(
            new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator(
                    __ROOT__ . '/app/Command/'
                )
            ),
            '/^.+\.php$/i',
            RecursiveRegexIterator::MATCH
        );

        foreach ($commandFiles as $commandFile) {
            if (strpos($commandFile->getBasename(), 'Abstract') !== false) {
                continue;
            }

            if (strpos($commandFile->getBasename(), 'Interface') !== false) {
                continue;
            }

            if (preg_match('/Command\/(.*)\/(.*).php$/', $commandFile->getPathname(), $matches) == 1) {
                $resource = str_replace('/', '\\', $matches[1]);
                $command  = sprintf('App\\Command\\%s\\%s', $resource, $matches[2]);
                $handler  = sprintf('App\\Handler\\%s', $resource);

                $commandList[$command] = $handler;
            }
        }

        $commandList[Command\ResponseDispatch::class] = Handler\Response::class;

        if (! empty($settings['boot']['commandsCache'])) {
            file_put_contents($settings['boot']['commandsCache'], serialize($commandList));
        }
    }

    $handlerMiddleware = new CommandHandlerMiddleware(
        new ClassNameExtractor(),
        new ContainerLocator(
            $container,
            $commandList
        ),
        new HandleClassNameInflector()
    );
    if ($settings['debug']) {
        $formatter = new ClassPropertiesFormatter();
    } else {
        $formatter = new ClassNameFormatter();
    }

    return new CommandBus(
        [
            new LoggerMiddleware(
                $formatter,
                $log('CommandBus')
            ),
            new TransactionMiddleware(
                $container->get('sql')
            ),
            $handlerMiddleware
        ]
    );
};

// App Command Factory
$container['commandFactory'] = function (ContainerInterface $container) : Factory\Command {
    return new Factory\Command();
};

// Validator Factory
$container['validatorFactory'] = function (ContainerInterface $container) : Factory\Validator {
    return new Factory\Validator();
};

// App Entity Factory
$container['entityFactory'] = function (ContainerInterface $container) : Factory\Entity {
    return new Factory\Entity($container->get('optimus'), $container->get('vault'));
};

// App Event Factory
$container['eventFactory'] = function (ContainerInterface $container) : Factory\Event {
    return new Factory\Event();
};

// Auth Middleware
$container['authMiddleware'] = function (ContainerInterface $container) : callable {
    return function ($authorizationRequirement) use ($container) {
        $repositoryFactory = $container->get('repositoryFactory');
        $jwt               = $container->get('jwt');

        return new Auth(
            $repositoryFactory->create('Company\\Credential'),
            $repositoryFactory->create('Identity'),
            $repositoryFactory->create('User'),
            $repositoryFactory->create('Company'),
            $repositoryFactory->create('Handler'),
            $jwt('parser'),
            $jwt('validation'),
            $jwt('signer'),
            $authorizationRequirement
        );
    };
};

// Permission Middleware
$container['endpointPermissionMiddleware'] = function (ContainerInterface $container) : callable {
    return function ($permissionType, $allowedRolesBits = 0x00) use ($container) : Middleware\EndpointPermission {
        return new Middleware\EndpointPermission(
            $container->get('repositoryFactory')->create('Company\Permission'),
            $container->get('repositoryFactory')->create('Company'),
            $permissionType,
            $allowedRolesBits
        );
    };
};

// AWS S3 Client
$container['S3Client'] = function (ContainerInterface $container) : S3Client {
    $settings = $container->get('settings');

    return S3Client::factory(
        [
            'credentials' => [
                'key'    => $settings['s3']['key'],
                'secret' => $settings['s3']['secret']
            ],
            'region'  => $settings['s3']['region'],
            'version' => $settings['s3']['version']
        ]
    );
};

// FlySystem
$container['fileSystem'] = function (ContainerInterface $container) : callable {
    return function (string $bucketName) use ($container) : Filesystem {
        $settings = $container->get('settings');

        if (empty($settings['fileSystem']['adapter'])) {
            throw new \RuntimeException('fileSystem:adapter is not set');
        }

        switch ($settings['fileSystem']['adapter']) {
            case 's3':
                $adapter = new AwsS3Adapter(
                    $container->get('S3Client'),
                    sprintf('idOS-%s', $bucketName)
                );
                break;
            case 'local':
                $adapter = new Local(
                    sprintf(
                        '%s/idOS-%s',
                        rtrim(
                            $settings['fileSystem']['path'] ?? '/tmp',
                            '/'
                        ),
                        $bucketName
                    )
                );
                break;
            case 'null':
                $adapter = new NullAdapter();
                break;
            default:
                throw new \RuntimeException('Invalid fileSystem:adapter');
        }

        if (! empty($settings['fileSystem']['cached'])) {
            $adapter = new CachedAdapter(
                $adapter,
                new Cache(
                    $container->get('cache'),
                    sprintf('idOS-%s', $bucketName),
                    300
                )
            );
        }

        $fileSystem = new Filesystem(
            $adapter,
            [
                'visibility' => AdapterInterface::VISIBILITY_PRIVATE
            ]
        );

        return $fileSystem->addPlugin(new ListFiles());
    };
};

// User Permission Middleware
$container['userPermissionMiddleware'] = function (ContainerInterface $container) : callable {
    return function ($resource, $resourceAccessLevel) use ($container) : Middleware\UserPermission {
        $roleAccessRepository = $container->get('repositoryFactory')->create('User\RoleAccess');

        return new Middleware\UserPermission($roleAccessRepository, $resource, $resourceAccessLevel);
    };
};

// App Repository Factory
$container['repositoryFactory'] = function (ContainerInterface $container) : Factory\Repository {
    $settings = $container->get('settings');
    switch ($settings['repository']['strategy']) {
        case 'db':
        default:
            $strategy = new Repository\DBStrategy(
                $container->get('entityFactory'),
                $container->get('optimus'),
                $container->get('vault'),
                $container->get('sql')
            );
    }

    if ((isset($settings['repository']['cached'])) && ($settings['repository']['cached'])) {
        return new Factory\Repository(
            $strategy,
            $container->get('cache')
        );
    }

    return new Factory\Repository($strategy);
};

// JSON Web Token
$container['jwt'] = function (ContainerInterface $container) : callable {
    return function ($item) use ($container) {
        switch ($item) {
            case 'builder':
                return new JWT\Builder();
            case 'parser':
                return new JWT\Parser();
            case 'validation':
                return new JWT\ValidationData();
            case 'signer':
                return new JWT\Signer\Hmac\Sha256();
            default:
                throw new \RuntimeException('Invalid JWT item!');
        }
    };
};

// DB Access
$container['sql'] = function (ContainerInterface $container) : Connection {
    $capsule = new Manager();
    $capsule->addConnection($container['settings']['sql']);

    return $capsule->getConnection();
};

// Respect Validator
$container['validator'] = function (ContainerInterface $container) : Validator {
    return Validator::create();
};

// Optimus
$container['optimus'] = function (ContainerInterface $container) : Optimus {
    $settings = $container->get('settings');

    return new Optimus(
        $settings['optimus']['prime'],
        $settings['optimus']['inverse'],
        $settings['optimus']['random']
    );
};

// App files
$container['globFiles'] = function () : array {
    return [
        'routes' => array_merge(
            glob(__ROOT__ . '/app/Route/*.php'),
            glob(__ROOT__ . '/app/Route/*/*.php')
        ),
        'handlers' => array_merge(
            glob(__ROOT__ . '/app/Handler/*.php'),
            glob(__ROOT__ . '/app/Handler/*/*.php')
        ),
        'eventListeners' => array_merge(
            glob(__ROOT__ . '/app/Listener/*Listener.php'),
            glob(__ROOT__ . '/app/Listener/*/*Listener.php')
        ),
        'listenerProviders' => array_merge(
            glob(__ROOT__ . '/app/Listener/*Provider.php'),
            glob(__ROOT__ . '/app/Listener/*/*Provider.php')
        )
    ];
};

// Vault
$container['vault'] = function (ContainerInterface $container) : Helper\Vault {
    $settings = $container->get('settings');

    $fileName = __ROOT__ . '/resources/secure.key';
    if (! is_file($fileName)) {
        throw new RuntimeException('Secure key not found!');
    }

    $encoded = file_get_contents($fileName);
    if (empty($encoded)) {
        throw new RuntimeException('Secure key could not be loaded!');
    }

    return new Helper\Vault($encoded, $settings['secure']);
};

// SSO Auth
$container['ssoAuth'] = function (ContainerInterface $container) : callable {
    return function ($provider, $key, $secret) use ($container) {
        $storage = new Memory();

        // Setup the credentials for the requests
        $credentials = new Credentials(
            $key,
            $secret,
            ''
        );

        $settings = $container->get('settings');

        $serviceFactory = new ServiceFactory();

        $client = new \OAuth\Common\Http\Client\CurlClient();
        $client->setCurlParameters([\CURLOPT_ENCODING => '']);
        $serviceFactory->setHttpClient($client);

        // Instantiate the service using the credentials, http client and storage mechanism for the token
        return $serviceFactory->createService(
            $provider,
            $credentials,
            $storage,
            $settings['sso_providers_scopes'][$provider]
        );
    };
};

// Social Settings
$container['socialSettings'] = function (ContainerInterface $container) : Helper\SocialSettings {
    $settings = $container->get('settings');

    return new Helper\SocialSettings(
        $container
            ->get('repositoryFactory')
            ->create('Company\\Setting'),
        $settings['social_tokens']
    );
};

// Gearman Client
$container['gearmanClient'] = function (ContainerInterface $container) : GearmanClient {
    try {
        $settings = $container->get('settings');
        $gearman  = new \GearmanClient();

        $gearman->addOptions(\GEARMAN_CLIENT_FREE_TASKS);

        if (isset($settings['gearman']['timeout'])) {
            $gearman->setTimeout($settings['gearman']['timeout']);
        }

        $gearman->addServers($settings['gearman']['servers']);

        return $gearman;
    } catch (\GearmanException $exception) {
        throw new AppException('Could not connect to Gearman Job Server');
    }
};

// HTTP Client
$container['httpClient'] = function (ContainerInterface $container) : HttpClient {
    return new HttpClient();
};

// Registering Event Emitter
$container['eventEmitter'] = function (ContainerInterface $container) : Emitter {
    return new Emitter();
};
