<?php
/*/*
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
 * Processes routing definitions.
 *
 * @link docs/profile/processes/overview.md
 * @see App\Controller\Processes
 */
class Processes implements RouteInterface {
    /**
     * {@inheritdoc}
     */
    public static function getPublicNames() : array {
        return [
            'processes:listAll',
            'processes:getOne',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function register(App $app) {
        $app->getContainer()[\App\Controller\Processes::class] = function (ContainerInterface $container) {
            return new \App\Controller\Processes(
                $container->get('repositoryFactory')->create('Process'),
                $container->get('commandBus'),
                $container->get('commandFactory')
            );
        };

        $container            = $app->getContainer();
        $authMiddleware       = $container->get('authMiddleware');
        $permissionMiddleware = $container->get('endpointPermissionMiddleware');

        self::listAll($app, $authMiddleware, $permissionMiddleware);
        self::getOne($app, $authMiddleware, $permissionMiddleware);
    }

    /**
     * List all Processes.
     *
     * Retrieve a complete list of all processes that belong to the given user.
     *
     * @apiEndpoint GET /profiles/{userName}/processes
     * @apiGroup Profile Processes
     * @apiAuth header key credentialToken  wqxehuwqwsthwosjbxwwsqwsdi A Valid Credential Token
     * @apiAuth query key credentialToken  wqxehuwqwsthwosjbxwwsqwsdi A Valid Credential Token
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/profile/processes/listAll.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Processes::listAll
     */
    private static function listAll(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/profiles/{userName:[a-zA-Z0-9_-]+}/processes',
                'App\Controller\Processes:listAll'
            )
            ->add($permission(EndpointPermission::PRIVATE_ACTION))
            ->add($auth(Auth::CREDENTIAL))
            ->setName('processes:listAll');
    }

    /**
     * Retrieve a single Process.
     *
     * Retrieves all public information from a Process.
     *
     * @apiEndpoint GET /profiles/{userName}/processes/{processId}
     * @apiGroup Profile Processes
     * @apiAuth header key CredentialToken  wqxehuwqwsthwosjbxwwsqwsdi A Valid Credential Token
     * @apiAuth query key credentialToken  wqxehuwqwsthwosjbxwwsqwsdi A Valid Credential Token
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/profile/processes/getOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Processes::getOne
     */
    private static function getOne(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/profiles/{userName:[a-zA-Z0-9_-]+}/processes/{processId:[0-9]+}',
                'App\Controller\Processes:getOne'
            )
            ->add($permission(EndpointPermission::PRIVATE_ACTION))
            ->add($auth(Auth::CREDENTIAL))
            ->setName('processes:getOne');
    }
}
