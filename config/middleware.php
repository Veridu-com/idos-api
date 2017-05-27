<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

use App\Middleware\Debugger;
use App\Middleware\GateKeeper;
use App\Middleware\OptimusDecode;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RKA\Middleware\IpAddress;
use Slim\HttpCache\Cache;
use Tuupola\Middleware\Cors;

if (! isset($app)) {
    die('$app is not set!');
}

$settings = $container->get('settings');
$logger   = $container->get('log');

$app
    ->add(new IpAddress(true, $settings['trustedProxies']))
    ->add(new OptimusDecode($app->getContainer()->get('optimus')))
    ->add(new GateKeeper($app->getContainer()))
    ->add(
        new Cors(
            [
                'origin'         => ['*'],
                'methods'        => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'],
                'headers.allow'  => [
                    'Authorization',
                    'Content-Type',
                    'If-Match',
                    'If-Modified-Since',
                    'If-Unmodified-Since',
                    'If-None-Match',
                    'X-Requested-With'
                ],
                'headers.expose' => [
                    'ETag',
                    'X-Rate-Limit-Limit',
                    'X-Rate-Limit-Remaining',
                    'X-Rate-Limit-Reset'
                ],
                'credentials'    => true,
                'cache'          => 3628800,
                'logger'         => $logger('CORS'),
                'error'          => function (ServerRequestInterface $request, ResponseInterface $response, $arguments) : ResponseInterface {
                    throw new \App\Exception\CorsError($arguments['message']);
                }
            ]
        )
    )
    ->add(new Cache('private, no-cache, no-store', 0, true))
    ->add(new Debugger());
