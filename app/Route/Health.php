<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Route;

use App\Middleware\Auth;
use App\Middleware\EndpointPermission;
use Interop\Container\ContainerInterface;
use Slim\App;

/**
 * Health check.
 *
 * This endpoint is used to ensure that the API and its services are responding properly.
 *
 * @apiDisabled
 *
 * @link docs/health/overview.md
 * @see \App\Controller\Health
 */
class Health implements RouteInterface {
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
        $app->getContainer()[\App\Controller\Health::class] = function (ContainerInterface $container) {
            return new \App\Controller\Health(
                $container->get('gearmanClient'),
                $container->get('sql'),
                $container->get('nosql'),
                $container->get('commandBus'),
                $container->get('commandFactory')
            );
        };

        $container            = $app->getContainer();
        $authMiddleware       = $container->get('authMiddleware');
        $permissionMiddleware = $container->get('endpointPermissionMiddleware');

        self::check($app, $authMiddleware, $permissionMiddleware);
    }

    /**
     * API health check.
     *
     * Checks the API health status.
     *
     * @apiEndpoint GET /health
     * @apiGroup Health
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/health/check.md
     * @see \App\Middleware\Auth::__invoke
     * @see \App\Middleware\Permission::__invoke
     * @see \App\Controller\Health::check
     */
    private static function check(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/health',
                'App\Controller\Health:check'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::NONE))
            ->setName('health:check');
    }
}
