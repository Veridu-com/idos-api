<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use App\Middleware\Debugger;
use App\Middleware\GateKeeper;
use App\Middleware\Watcher;
use App\Middleware\OptimusDecode;
use Slim\HttpCache\Cache;

if (! isset($app)) {
    die('$app is not set!');
}

$optimus = $app->getContainer()->get('optimus');

$app
    ->add(new OptimusDecode($optimus))
    ->add(new GateKeeper($app->getContainer()))
    ->add(new Watcher($app->getContainer()))
    ->add(new Cache('private, no-cache, no-store', 0, true))
    ->add(new Debugger());
