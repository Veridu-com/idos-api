<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

use Apix\Cache;
use App\Command;
use App\Event\ListenerProvider;
use App\Exception\AppException;
use App\Factory;
use App\Handler;
use App\Middleware;
use App\Middleware\Auth;
use App\Repository;
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
use OAuth\Common\Http\Uri\UriFactory;
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
        $log('Foundation')->error($exception->getTraceAsString());

        if ($exception instanceof AppException) {
            $log('API')->info(
                sprintf(
                    '%s [%s:%d]',
                    $exception->getMessage(),
                    $exception->getFile(),
                    $exception->getLine()
                )
            );

            $body = [
                'status' => false,
                'error'  => [
                    'code'    => $exception->getCode(),
                    'type'    => 'EXCEPTION_TYPE', // $exception->getType(),
                    'link'    => 'https://docs.idos.io/errors/EXCEPTION_TYPE', // $exception->getLink(),
                    'message' => $exception->getMessage(),
                    'trace'   => $exception->getTrace()
                ]
            ];

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

        $settings = $container->get('settings');
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
        throw new \Exception('Whoopsies! Route not found!', 404);
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

        throw new \Exception('Whoopsies! Method not allowed for this route!', 400);
    };
};

// Monolog Logger
$container['log'] = function (ContainerInterface $container) : callable {
    return function ($channel = 'API') use ($container) {
        $settings = $container->get('settings');
        $logger   = new Logger($channel);
        $logger
            ->pushProcessor(new UidProcessor())
            ->pushProcessor(new WebProcessor())
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
    $logger   = new Logger('CommandBus');
    $logger
        ->pushProcessor(new UidProcessor())
        ->pushProcessor(new WebProcessor())
        ->pushHandler(new StreamHandler($settings['log']['path'], $settings['log']['level']));

    $commandPaths = glob(__DIR__ . '/../app/Command/*/*.php');
    $commands     = [];
    foreach ($commandPaths as $commandPath) {
        $matches = [];
        preg_match_all('/.*Command\/(.*)\/(.*).php/', $commandPath, $matches);

        $resource = $matches[1][0];
        $command  = $matches[2][0];

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
                $logger
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

// Auth Middleware
$container['authMiddleware'] = function (ContainerInterface $container) : callable {
    return function ($authorizationRequirement) use ($container) {
        $repositoryFactory = $container->get('repositoryFactory');
        $jwt               = $container->get('jwt');

        return new Auth(
            $repositoryFactory->create('Credential'),
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
    return function ($permissionType) use ($container) {
        return new Middleware\EndpointPermission(
            $container->get('repositoryFactory')->create('Permission'),
            $container->get('repositoryFactory')->create('Company'),
            $permissionType
        );
    };
};

// User Permission Middleware
$container['userPermissionMiddleware'] = function (ContainerInterface $container) {
    return function ($resource, $resourceAccessLevel) use ($container) {
        $roleAccessRepository = $container->get('repositoryFactory')->create('RoleAccess');

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
        'routes'            => glob(__DIR__ . '/../app/Route/*.php'),
        'handlers'          => glob(__DIR__ . '/../app/Handler/*.php'),
        'listenerProviders' => glob(__DIR__ . '/../app/Listener/*/*Provider.php'),
    ];
};

// Register Event emitter & Event listeners
$container['eventEmitter'] = function (ContainerInterface $container) : Emitter {
    $emitter = new Emitter();

    $providers = array_map(
        function ($providerFile) {
            return preg_replace(
                '/.*?Listener\/(.*)\/ListenerProvider.php/',
                'App\\Listener\\\$1\\ListenerProvider',
                $providerFile
            );
        },
        $container->get('globFiles')['listenerProviders']
    );

    foreach ($providers as $provider) {
        $emitter->useListenerProvider(new $provider($container));
    }

    return $emitter;
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
        $uriFactory = new UriFactory();
        $currentUri = $uriFactory->createFromSuperGlobalArray($_SERVER);
        $currentUri->setQuery('');

        $storage = new Memory();

        // Setup the credentials for the requests
        $credentials = new Credentials(
            $key,
            $secret,
            $currentUri->getAbsoluteUri()
        );

        $settings = $container->get('settings');

        $serviceFactory = new ServiceFactory();

        $client = new \OAuth\Common\Http\Client\CurlClient();
        $client->setCurlParameters([\CURLOPT_ENCODING => '']);
        $serviceFactory->setHttpClient($client);

        // Instantiate the service using the credentials, http client and storage mechanism for the token
        return $serviceFactory->createService($provider, $credentials, $storage, $settings['sso_providers_scopes'][$provider]);
    };
};
