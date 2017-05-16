<?php
/*/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Route\Profile;

use App\Controller\ControllerInterface;
use App\Middleware\Auth;
use App\Middleware\EndpointPermission;
use App\Route\RouteInterface;
use Interop\Container\ContainerInterface;
use Slim\App;

/**
 * Profile Processes.
 *
 * The Profile Process is the process the API uses in order to extract information from the raw data provided by the
 * User. This Process is broken up by the Services that perform Tasks.
 *
 * **Note:** advanced usage only.
 *
 * @link docs/profile/processes/overview.md
 * @see \App\Controller\Profile\Processes
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
    public static function register(App $app) : void {
        $app->getContainer()[\App\Controller\Profile\Processes::class] = function (ContainerInterface $container) : ControllerInterface {
            return new \App\Controller\Profile\Processes(
                $container->get('repositoryFactory')->create('Profile\Process'),
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
     * @apiGroup Profile
     * @apiAuth header token CredentialToken  wqxehuwqwsthwosjbxwwsqwsdi A Valid Credential Token
     * @apiAuth query token CredentialToken  wqxehuwqwsthwosjbxwwsqwsdi A Valid Credential Token
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/profile/processes/listAll.md
     * @see \App\Middleware\Auth::__invoke
     * @see \App\Middleware\Permission::__invoke
     * @see \App\Controller\Profile\Processes::listAll
     */
    private static function listAll(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/profiles/{userName:[a-zA-Z0-9_-]+}/processes',
                'App\Controller\Profile\Processes:listAll'
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
     * @apiGroup Profile
     * @apiAuth header token CredentialToken  wqxehuwqwsthwosjbxwwsqwsdi A Valid Credential Token
     * @apiAuth query token credentialToken  wqxehuwqwsthwosjbxwwsqwsdi A Valid Credential Token
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     * @apiEndpointURIFragment int processId 3412
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/profile/processes/getOne.md
     * @see \App\Middleware\Auth::__invoke
     * @see \App\Middleware\Permission::__invoke
     * @see \App\Controller\Profile\Processes::getOne
     */
    private static function getOne(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/profiles/{userName:[a-zA-Z0-9_-]+}/processes/{processId:[0-9]+}',
                'App\Controller\Profile\Processes:getOne'
            )
            ->add($permission(EndpointPermission::PRIVATE_ACTION))
            ->add($auth(Auth::CREDENTIAL))
            ->setName('processes:getOne');
    }
}
