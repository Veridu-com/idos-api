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
    'routerCacheFile'                   => Env::asString('IDOS_ROUTER_CACHE', '') ?: false,
    'determineRouteBeforeAppMiddleware' => true,
    'trustedProxies'                    => Env::asArray('IDOS_TRUSTED_PROXIES', ['127.0.0.1']),
    'boot'                              => [
        'commandsCache'  => Env::asString('IDOS_COMMANDS_CACHE', ''),
        'handlersCache'  => Env::asString('IDOS_HANDLERS_CACHE', ''),
        'listenersCache' => Env::asString('IDOS_LISTENERS_CACHE', ''),
        'providersCache' => Env::asString('IDOS_PROVIDERS_CACHE', ''),
        'routesCache'    => Env::asString('IDOS_ROUTES_CACHE', '')
    ],
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
    'log' => [
        'path' => Env::asString(
            'IDOS_LOG_FILE',
            sprintf(
                '%s/../log/api.log',
                __DIR__
            )
        ),
        'level' => Monolog\Logger::DEBUG
    ],
    'cache' => [
        'driver'  => Env::asString('IDOS_CACHE_DRIVER', 'ephemeral'),
        'options' => [
            'path'    => Env::asString('IDOS_CACHE_PATH', ''),
            'servers' => Env::fromJson('IDOS_CACHE_SERVERS', [])
        ]
    ],
    'gearman' => [
        'timeout' => 1000,
        'servers' => Env::asString('IDOS_GEARMAN_SERVERS', 'localhost:4730')
    ],
    'optimus' => [
        'prime'   => Env::asInteger('IDOS_OPTIMUS_PRIME', 0),
        'inverse' => Env::asInteger('IDOS_OPTIMUS_INVERSE', 0),
        'random'  => Env::asInteger('IDOS_OPTIMUS_RANDOM', 0)
    ],
    'repository' => [
        'strategy' => 'db',
        'cached'   => Env::asString('IDOS_CACHE_DRIVER', 'ephemeral') !== 'ephemeral'
    ],
    'fileSystem' => [
        'adapter' => Env::asString('IDOS_FS_ADAPTER', ''),
        'cached'  => Env::asBool('IDOS_FS_CACHED', false),
        'path'    => Env::asString('IDOS_FS_PATH', '/tmp')
    ],
    's3' => [
        'key'     => Env::asString('IDOS_S3_KEY', ''),
        'secret'  => Env::asString('IDOS_S3_SECRET', ''),
        'region'  => Env::asString('IDOS_S3_REGION', ''),
        'version' => Env::asString('IDOS_S3_VERSION', '')
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
    ],
    'social_tokens' => [
        'amazon' => [
            'sso'    => false,
            'key'    => '***REMOVED***',
            'secret' => '***REMOVED***',
            'scope'  => [
                'profile', 'postal_code'
            ]
        ],
        'dropbox' => [
            'sso'    => false,
            'key'    => '***REMOVED***',
            'secret' => '***REMOVED***',
            'scope'  => []
        ],
        'facebook' => [
            'sso'    => true,
            'key'    => '***REMOVED***',
            'secret' => '***REMOVED***',
            'scope'  => [
                'email', 'public_profile', 'user_friends'
            ],
            'options' => [
                'display' => 'popup'
            ],
            'version' => '2.0'
        ],
        'google' => [
            'sso'    => true,
            'key'    => '***REMOVED***',
            'secret' => '***REMOVED***',
            'scope'  => [
                'openid', 'profile', 'email',
                'https://www.googleapis.com/auth/plus.me',
                'https://www.googleapis.com/auth/plus.login',
                'https://www.googleapis.com/auth/gmail.readonly',
                'https://www.google.com/m8/feeds/',
                'https://www.googleapis.com/auth/drive.apps.readonly',
                'https://www.googleapis.com/auth/drive.metadata.readonly',
                'https://www.googleapis.com/auth/drive.readonly'
            ]
        ],
        'instagram' => [
            'sso'    => false,
            'key'    => '***REMOVED***',
            'secret' => '***REMOVED***',
            'scope'  => [
                'basic'
            ]
        ],
        'linkedin' => [
            'sso'    => true,
            'key'    => '***REMOVED***',
            'secret' => '***REMOVED***',
            'scope'  => [
                'r_basicprofile', 'r_emailaddress'
            ]
        ],
        'paypal' => [
            'sso'    => true,
            'key'    => '***REMOVED***',
            'secret' => '***REMOVED***',
            'scope'  => [
                'https://uri.paypal.com/services/paypalattributes',
                'profile', 'email', 'address', 'phone', 'openid'
            ]
        ],
        'spotify' => [
            'sso'    => false,
            'key'    => '***REMOVED***',
            'secret' => '***REMOVED***',
            'scope'  => [
                'playlist-read-private', 'playlist-read-collaborative', 'user-follow-read',
                'user-library-read', 'user-read-private', 'user-read-birthdate', 'user-read-email'
            ]
        ],
        'twitter' => [
            'sso'    => false,
            'key'    => '***REMOVED***',
            'secret' => '***REMOVED***'
        ],
        'yahoo' => [
            'sso'    => false,
            'key'    => '***REMOVED***',
            'secret' => '***REMOVED***'
        ]
    ]
];
