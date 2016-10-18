<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

use Apix\Cache;
use App\Command;
use App\Exception\AppException;
use App\Factory;
use App\Handler;
use App\Middleware;
use App\Middleware\Auth;
use App\Repository;
use GuzzleHttp\Client as HttpClient;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Connection;
use Interop\Container\ContainerInterface;
use Jenssegers\Mongodb;
use Jenssegers\Optimus\Optimus;
use Lcobucci\JWT;
use League\Event\Emitter;
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
use Stash\Driver\FileSystem;
use Stash\Driver\Redis;
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
        \Exception $exception
    ) use ($container) {
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

// Slim Not Found Handler
$container['notFoundHandler'] = function (ContainerInterface $container) : callable {
    return function (
        ServerRequestInterface $request,
        ResponseInterface $response
    ) use ($container) {
        throw new AppException('Whoopsies! Route not found!', 404);
    };
};

// Slim Not Allowed Handler
$container['notAllowedHandler'] = function (ContainerInterface $container) : callable {
    return function (
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $methods
    ) use ($container) {
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
    return function ($channel = 'API') use ($container) {
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
$container['cache'] = function (ContainerInterface $container) : Cache\PsrCache\TaggablePool {
    $settings = $container->get('settings');

    if (empty($settings['cache']['driver'])) {
        $settings['cache']['driver'] = 'filesystem';
    }

    switch ($settings['cache']['driver']) {
        case 'filesystem':
            $options = array_merge($settings['cache']['default'], $settings['cache']['directory']);
            $pool    = Cache\Factory::getTaggablePool(new Cache\Directory(), $options);
            break;
        case 'redis':
            $options = array_merge($settings['cache']['default'], $settings['cache']['redis']);
            $redis   = new \Redis();
            $redis->connect($settings['cache']['redis']['host'], $settings['cache']['redis']['port']);
            $pool = Cache\Factory::getTaggablePool($redis, $options);
            break;
    }

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

    $commandPaths = array_merge(glob(__DIR__ . '/../app/Command/*/*.php'), glob(__DIR__ . '/../app/Command/*/*/*.php'));
    $commands     = [];
    foreach ($commandPaths as $commandPath) {
        $matches = [];
        preg_match_all('/.*Command\/(.*)\/(.*).php/', $commandPath, $matches);

        $resource = preg_replace("/\//", '\\', $matches[1][0]);
        // $resource = $matches[1][0];
        $command = $matches[2][0];

        $commands[sprintf('App\\Command\\%s\\%s', $resource, $command)] = sprintf('App\\Handler\\%s', $resource);
    }

    $commands[Command\ResponseDispatch::class] = Handler\Response::class;
    $handlerMiddleware                         = new CommandHandlerMiddleware(
        new ClassNameExtractor(),
        new ContainerLocator(
            $container,
            $commands
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
    return new Factory\Entity($container->get('optimus'));
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
            $repositoryFactory->create('Service'),
            $jwt('parser'),
            $jwt('validation'),
            $jwt('signer'),
            $authorizationRequirement
        );
    };
};

// Permission Middleware
$container['endpointPermissionMiddleware'] = function (ContainerInterface $container) : callable {
    return function ($permissionType, $allowedRolesBits = 0x00) use ($container) {
        return new Middleware\EndpointPermission(
            $container->get('repositoryFactory')->create('Company\Permission'),
            $container->get('repositoryFactory')->create('Company'),
            $permissionType,
            $allowedRolesBits
        );
    };
};

// User Permission Middleware
$container['userPermissionMiddleware'] = function (ContainerInterface $container) {
    return function ($resource, $resourceAccessLevel) use ($container) {
        $roleAccessRepository = $container->get('repositoryFactory')->create('User\RoleAccess');

        return new Middleware\UserPermission($roleAccessRepository, $resource, $resourceAccessLevel);
    };
};

// Permission Middleware
$container['optimusDecodeMiddleware'] = function (ContainerInterface $container) {
    return function ($permissionType) use ($container) {
        return new Middleware\OptimusDecode($container->get('optimus'));
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
                $container->get('sql'),
                $container->get('nosql')
            );
    }

    if ((isset($settings['repository']['cached'])) && ($settings['repository']['cached'])) {
        $strategy = new Repository\CachedStrategy(
            new Factory\Repository($strategy),
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
        }
    };
};

// DB Access
$container['sql'] = function (ContainerInterface $container) : Connection {
    $capsule = new Manager();
    $capsule->addConnection($container['settings']['db']['sql']);

    return $capsule->getConnection();
};

// MongoDB Access
$container['nosql'] = function (ContainerInterface $container) : callable {
    return function (string $database) use ($container) : Mongodb\Connection {
        $config             = $container['settings']['db']['nosql'];
        $config['database'] = $database;

        return new Mongodb\Connection($config);
    };
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
            glob(__DIR__ . '/../app/Route/*.php'),
            glob(__DIR__ . '/../app/Route/*/*.php')
        ),
        'handlers' => array_merge(
            glob(__DIR__ . '/../app/Handler/*.php'),
            glob(__DIR__ . '/../app/Handler/*/*.php')
        ),
        'listenerProviders' => glob(__DIR__ . '/../app/Listener/*/*Provider.php'),
    ];
};

// Secure
$container['secure'] = function (ContainerInterface $container) : Secure {
    $fileName = __DIR__ . '/../resources/secure.key';
    if (! is_file($fileName)) {
        throw new RuntimeException('Secure key not found!');
    }

    $encoded = file_get_contents($fileName);
    if (empty($encoded)) {
        throw new RuntimeException('Secure key could not be loaded!');
    }

    return new Secure($encoded, $settings['secure']);
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

// Gearman Client
$container['gearmanClient'] = function (ContainerInterface $container) : GearmanClient {
    try {
        $settings = $container->get('settings');
        $gearman  = new \GearmanClient();
        if (isset($settings['gearman']['timeout'])) {
            $gearman->setTimeout($settings['gearman']['timeout']);
        }

        foreach ($settings['gearman']['servers'] as $server) {
            if (is_array($server)) {
                $gearman->addServer($server[0], $server[1]);
            } else {
                $gearman->addServer($server);
            }
        }

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
