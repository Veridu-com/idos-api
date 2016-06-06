<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Route;

use Interop\Container\ContainerInterface;
use Slim\App;

/**
 * Root routing definitions.
 */
class Main implements RouteInterface {
    /**
     * {@inheritDoc}
     */
    public static function getPublicNames() {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public static function register(App $app) {
        $app->getContainer()[\App\Controller\Main::class] = function (ContainerInterface $container) {
            return new \App\Controller\Main(
                $container->get('router'),
                $container->get('commandBus'),
                $container->get('commandFactory')
            );
        };

        // [GET /1.0/](listAll.md)
        $app
            ->get(
                '/',
                'App\Controller\Main:listAll'
            )
            ->setName('main:listAll');
    }
}
