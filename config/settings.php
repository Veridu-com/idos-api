<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

use App\Helper\Env;

if (! defined('__VERSION__')) {
    define('__VERSION__', Env::asString('IDOS_VERSION', '1.0'));
}

$appSettings = [
    'debug'                             => Env::asBool('IDOS_DEBUG', false),
    'displayErrorDetails'               => Env::asBool('IDOS_DEBUG', false),
    'determineRouteBeforeAppMiddleware' => true,
    'trustedProxies'                    => Env::asArray('IDOS_TRUSTED_PROXIES', ['127.0.0.1']),
    'db'                                => [
        'sql' => [
            'driver'    => Env::asString('IDOS_SQL_DRIVER', 'pgsql'),
            'host'      => Env::asString('IDOS_SQL_HOST', 'localhost'),
            'port'      => Env::asInteger('IDOS_SQL_PORT', 5432),
            'database'  => Env::asString('IDOS_SQL_NAME', 'idos-api'),
            'username'  => Env::asString('IDOS_SQL_USER', 'idos-api'),
            'password'  => Env::asString('IDOS_SQL_PASS', 'idos-api'),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'options'   => [
                \PDO::ATTR_TIMEOUT    => 5,
                \PDO::ATTR_PERSISTENT => false
            ]
        ],
        'nosql' => [
            'driver' => Env::asString('IDOS_NOSQL_DRIVER', 'mongodb'),
            'host'   => Env::asString('IDOS_NOSQL_HOST', 'localhost'),
            'port'   => Env::asInteger('IDOS_NOSQL_PORT', 27017)
        ]
    ],
    'log' => [
        'path' => sprintf(
            '%s/../log/api.log',
            __DIR__
        ),
        'level' => Monolog\Logger::DEBUG
    ],
    'cache' => [
        'driver' => 'ephemeral'
    ],
    'gearman' => [
        'timeout' => 1000,
        'servers' => Env::fromJson('IDOS_GEARMAN_SERVERS', [['localhost', 4730]])
    ],
    'optimus' => [
        'prime'   => Env::asInteger('IDOS_OPTIMUS_PRIME', 0),
        'inverse' => Env::asInteger('IDOS_OPTIMUS_INVERSE', 0),
        'random'  => Env::asInteger('IDOS_OPTIMUS_RANDOM', 0)
    ],
    'repository' => [
        'strategy' => 'db',
        'cached'   => false
    ],
    'secure'        => Env::asString('IDOS_SECURE_KEY', ''),
    'sso_providers' => [
        'amazon',
        'facebook',
        'google',
        'linkedin',
        'paypal',
        'twitter',
    ],
    'sso_providers_scopes' => [
        'amazon'   => ['profile'],
        'facebook' => ['email', 'public_profile', 'user_friends'],
        'google'   => ['userinfo_email', 'userinfo_profile'],
        'linkedin' => ['r_basicprofile'],
        'paypal'   => ['profile', 'openid'],
        'twitter'  => []
    ]
];