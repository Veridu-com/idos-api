<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

if (! isset($app)) {
    die('$app is not set!');
}

$handlers = $app->getContainer()->globFiles['handlers'];

foreach ($handlers as $file) {
    if (preg_match('/(Abstract|Interface)/', $file)) {
        continue;
    }

    $className =
        preg_replace(
            '/^\\\app/',
            '',
            str_replace(
                '.php',
                '',
                str_replace(
                    '/',
                    '\\',
                    str_replace(
                        '/config/../app/Handler/',
                        '/App/Handler/',
                        $file
                    )
                )
            )
        );

    if (class_exists($className)) {
        $className::register($container);
    }
}
