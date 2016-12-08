<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Route;

use App\Middleware\EndpointPermission;
use Interop\Container\ContainerInterface;
use Slim\App;

/**
 * General Purpose Endpoints.
 *
 * Comprehensive list of secondary endpoints.
 *
 * @link docs/overview.md
 * @see \App\Controller\Main
 */
class Main implements RouteInterface {
    /**
     * {@inheritdoc}
     */
    public static function getPublicNames() : array {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public static function register(App $app) {
        $app->getContainer()[\App\Controller\Main::class] = function (ContainerInterface $container) {
            return new \App\Controller\Main(
                $container->get('globFiles')['routes'],
                $container->get('router'),
                $container->get('commandBus'),
                $container->get('commandFactory')
            );
        };

        $permissionMiddleware = $app->getContainer()->get('endpointPermissionMiddleware');

        self::listAll($app, $permissionMiddleware);
    }

    /**
     * List all Endpoints.
     *
     * Retrieve a complete list with all available endpoints and call methods.
     *
     * @apiEndpoint GET /
     * @apiGroup General
     * @apiEndpointResponse 200 schema/listAll.json
     *
     * @param \Slim\App $app
     *
     * @return void
     *
     * @link docs/listAll.md
     * @see \App\Controller\Main::listAll
     */
    private static function listAll(App $app, callable $permission) {
        $app
            ->get(
                '/',
                'App\Controller\Main:listAll'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->setName('main:listAll');
    }
}
