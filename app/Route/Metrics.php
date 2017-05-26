<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Route;

use App\Controller\ControllerInterface;
use App\Entity\Role;
use App\Middleware\Auth;
use App\Middleware\EndpointPermission;
use Interop\Container\ContainerInterface;
use Slim\App;

/**
 * Metrics.
 *
 * Metrics is what the API uses to document its activity when it is in operation.
 * It also gathers information about how the User interacts with the API when they make a verification.
 * This is used for collecting data on the verification process for analysing trends or creating visualisations.
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
    public static function register(App $app) : void {
        $app->getContainer()[\App\Controller\Metrics::class] = function (ContainerInterface $container) : ControllerInterface {
            $repositoryFactory = $container->get('repositoryFactory');

            return new \App\Controller\Metrics(
                $repositoryFactory
                    ->create('Metric\System'),
                $repositoryFactory
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
     * @apiGroup Metrics
     * @apiAuth header token IdentityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     * @apiAuth query token IdentityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     * @apiEndpointURIFragment string companySlug veridu-ltd
     *
     * @param \Slim\App $app
     * @param callable  $auth
     * @param callable  $permission
     *
     * @return void
     *
     * @link docs/metrics/listAllSystem.md
     * @see \App\Middleware\Auth::__invoke
     * @see \App\Middleware\Permission::__invoke
     * @see \App\Controller\Metrics::listAllSystem
     */
    private static function listAllSystem(App $app, callable $auth, callable $permission) : void {
        $app
            ->get(
                '/companies/{companySlug:[a-z0-9_-]+}/metrics/system',
                'App\Controller\Metrics:listAllSystem'
            )
            ->add(
                $permission(
                    EndpointPermission::PARENT_ACTION | EndpointPermission::SELF_ACTION,
                    Role::COMPANY_OWNER_BIT | Role::COMPANY_ADMIN_BIT
                )
            )
            ->add($auth(Auth::IDENTITY))
            ->setName('metric:listAllSystem');
    }

    /**
     * List all user metrics.
     *
     * Retrieve a complete list of the user metrics.
     *
     * @apiEndpoint GET /metrics/user
     * @apiGroup Metrics
     * @apiAuth header token IdentityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     * @apiAuth query token IdentityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     * @apiEndpointURIFragment string companySlug veridu-ltd
     *
     * @param \Slim\App $app
     * @param callable  $auth
     * @param callable  $permission
     *
     * @return void
     *
     * @link docs/metrics/listAllUser.md
     * @see \App\Middleware\Auth::__invoke
     * @see \App\Middleware\Permission::__invoke
     * @see \App\Controller\Metrics::listAllUser
     */
    private static function listAllUser(App $app, callable $auth, callable $permission) : void {
        $app
            ->get(
                '/companies/{companySlug:[a-z0-9_-]+}/metrics/user',
                'App\Controller\Metrics:listAllUser'
            )
            ->add(
                $permission(
                    EndpointPermission::PARENT_ACTION | EndpointPermission::SELF_ACTION,
                    Role::COMPANY_OWNER_BIT | Role::COMPANY_ADMIN_BIT
                )
            )
            ->add($auth(Auth::IDENTITY))
            ->setName('metric:listAllUser');
    }
}
