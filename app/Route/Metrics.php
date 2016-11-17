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
 * Profile routing definitions.
 *
 * @link docs/metrics/overview.md
 * @see \App\Controller\Metrics
 */
class Metrics implements RouteInterface {
    /**
     * {@inheritdoc}
     */
    public static function getPublicNames() : array {
        return [
            'metric:listAllSystem',
            'metric:listAllUser'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function register(App $app) {
        $app->getContainer()[\App\Controller\Metrics::class] = function (ContainerInterface $container) {
            return new \App\Controller\Metrics(
                $container
                    ->get('repositoryFactory')
                    ->create('Metric\System'),
                $container
                    ->get('repositoryFactory')
                    ->create('Metric\User'),
                $container
                    ->get('commandBus'),
                $container
                    ->get('commandFactory')
            );
        };

        $container            = $app->getContainer();
        $authMiddleware       = $container->get('authMiddleware');
        $permissionMiddleware = $container->get('endpointPermissionMiddleware');

        self::listAllSystem($app, $authMiddleware, $permissionMiddleware);
        self::listAllUser($app, $authMiddleware, $permissionMiddleware);
    }

    /**
     * List all system metrics.
     *
     * Retrieve a complete list of the system metrics.
     *
     * @apiEndpoint GET /metrics/system
     * @apiGroup Profile
     * @apiAuth header token IdentityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     * @apiAuth query token IdentityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/metrics/listAllSystem.md
     * @see \App\Middleware\Auth::__invoke
     * @see \App\Middleware\Permission::__invoke
     * @see \App\Controller\Metrics::listAllSystem
     */
    private static function listAllSystem(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/metrics/system',
                'App\Controller\Metrics:listAllSystem'
            )
            ->add($permission(EndpointPermission::SELF_ACTION))
            ->add($auth(Auth::IDENTITY))
            ->setName('metric:listAllSystem');
    }

    /**
     * List all user metrics.
     *
     * Retrieve a complete list of the user metrics.
     *
     * @apiEndpoint GET /metrics/user
     * @apiGroup Profile
     * @apiAuth header token IdentityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     * @apiAuth query token IdentityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/metrics/listAllUser.md
     * @see \App\Middleware\Auth::__invoke
     * @see \App\Middleware\Permission::__invoke
     * @see \App\Controller\Metrics::listAllUser
     */
    private static function listAllUser(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/metrics/user',
                'App\Controller\Metrics:listAllUser'
            )
            ->add($permission(EndpointPermission::SELF_ACTION))
            ->add($auth(Auth::IDENTITY))
            ->setName('metric:listAllUser');
    }
}
