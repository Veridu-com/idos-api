<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Route;

use App\Middleware\CompanyPermission;
use Interop\Container\ContainerInterface;
use Slim\App;

/**
 * Root routing definitions.
 *
 * @link docs/overview.md
 * @see App\Controller\Main
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
                $container->get('router'),
                $container->get('commandBus'),
                $container->get('commandFactory')
            );
        };

        $permissionMiddleware = $app->getContainer()->get('permissionMiddleware');

        self::listAll($app, $permissionMiddleware);
    }

    /**
     * List all Endpoints.
     *
     * Retrieve a complete list of all public endpoints.
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
     * @see App\Controller\Main::listAll
     */
    private static function listAll(App $app, callable $permission) {
        $app
            ->get(
                '/',
                'App\Controller\Main:listAll'
            )
            ->add($permission(CompanyPermission::PUBLIC_ACTION))
            ->setName('main:listAll');
    }
}
