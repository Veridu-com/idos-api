<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

use App\Middleware\CORS;
use App\Middleware\Debugger;
use App\Middleware\GateKeeper;
use App\Middleware\OptimusDecode;
use App\Middleware\Watcher;
use RKA\Middleware\IpAddress;
use Slim\HttpCache\Cache;

if (! isset($app)) {
    die('$app is not set!');
}

$settings = $container->get('settings');

$app
    ->add(new IpAddress(true, $settings['trustedProxies']))
    ->add(new OptimusDecode($app->getContainer()->get('optimus')))
    ->add(new GateKeeper($app->getContainer()))
    ->add(new CORS(['GET', 'PUT', 'PATCH', 'DELETE', 'POST', 'OPTIONS']))
    ->add(new Watcher($app->getContainer()))
    ->add(new Cache('private, no-cache, no-store', 0, true))
    ->add(new Debugger());
