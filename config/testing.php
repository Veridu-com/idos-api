<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

$appSettings = [
    'debug'                             => false,
    'displayErrorDetails'               => false,
    'determineRouteBeforeAppMiddleware' => true,
    'trustedProxies'                    => [
    ],
    'db' => [
        'sql' => [
            'driver' => 'pgsql',
            // 'read' => [
            //     'host' => 'localhost'
            // ],
            // 'write' => [
            //     'host' => 'localhost'
            // ],
            'host'      => 'localhost',
            'port'      => 5432,
            'database'  => 'veridu-api',
            'username'  => 'veridu-api',
            'password'  => 'veridu-api',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'options'   => [
                \PDO::ATTR_TIMEOUT    => 5,
                \PDO::ATTR_PERSISTENT => true
            ]
        ],

        'nosql' => [
            'driver' => 'mongodb',
            'host'   => 'localhost',
            'port'   => 27017
            //'database'  => 'veridu-idos-api',
            //'username'  => 'veridu',
            //'password'  => 'veridu',
        ]
    ],
    'log' => [
        'path'  => sprintf('%s/../log/testing-%04d%02d%02d.log', __DIR__, date('Y'), date('m'), date('d')),
        'level' => Monolog\Logger::DEBUG
    ],
    'cache' => [
        'driver' => 'ephemeral'
    ],
    'gearman' => [
        'timeout' => 1000,
        'servers' => [
            ['localhost', 4730]
        ]
    ],
    'mongo' => [
        'dsn' => 'mongodb:///tmp/mongodb-27017.sock'
    ],
    'optimus' => [
        'prime'   => 0,
        'inverse' => 0,
        'random'  => 0
    ],
    'strategy' => [
        'repository' => 'array'
    ]
];
