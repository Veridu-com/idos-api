<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

if (! isset($app)) {
    die('$app is not set!');
}

$container = $app->getContainer();
$settings  = $container->get('settings');

$classList = [];
if ((! empty($settings['boot']['routesCache'])) && (is_file($settings['boot']['routesCache']))) {
    $cache = file_get_contents($settings['boot']['routesCache']);
    if ($cache !== false) {
        $cache = unserialize($cache);
    }

    if ($cache !== false) {
        $classList = $cache;
    }
}

if (empty($classList)) {
    $routeFiles = new RegexIterator(
        new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(
                __ROOT__ . '/app/Route/'
            )
        ),
        '/^.+\.php$/i',
        RecursiveRegexIterator::MATCH
    );

    $pathLen = strlen(__ROOT__ . '/app/Route/');
    foreach ($routeFiles as $routeFile) {
        if (strpos($routeFile->getBasename(), 'Abstract') !== false) {
            continue;
        }

        if (strpos($routeFile->getBasename(), 'Interface') !== false) {
            continue;
        }

        $className = str_replace(
            [
                '/',
                '.php'
            ],
            [
                '\\',
                ''
            ],
            sprintf(
                'App\\Route\\%s',
                substr(
                    $routeFile->getPathname(),
                    $pathLen
                )
            )
        );

        $classList[] = $className;
    }

    if (! empty($settings['boot']['routesCache'])) {
        file_put_contents($settings['boot']['routesCache'], serialize($classList));
    }
}

$app->get(
    '/',
    function () {
        throw new \Exception('what does the fox says?');
    }
);

$app->group(
    '/' . __VERSION__,
    function () use ($classList) {
        foreach ($classList as $className) {
            if (class_exists($className)) {
                $className::register($this);
            }
        }
    }
);
