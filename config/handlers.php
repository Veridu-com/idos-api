<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

if (! isset($app)) {
    die('$app is not set!');
}

$container = $app->getContainer();

foreach (glob(__DIR__ . '/../app/Handler/*.php') as $file) {
    if (preg_match('/(Abstract|Interface)/', $file)) {
        continue;
    }

    $className = sprintf('\\App\\Handler\\%s', str_replace('.php', '', basename($file)));
    if (class_exists($className)) {
        $className::register($container);
    }
}
