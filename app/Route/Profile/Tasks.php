<?php
/*/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Route\Profile;

use App\Middleware\Auth;
use App\Middleware\EndpointPermission;
use App\Route\RouteInterface;
use Interop\Container\ContainerInterface;
use Slim\App;

/**
 * Profile Tasks.
 *
 * A Profile Task is a request a Service makes to the API to extract and provide specific information from the Raw data.
 *
 * @link docs/profile/tasks/overview.md
 * @see App\Controller\Profile\Tasks
 */
class Tasks implements RouteInterface {
    /**
     * {@inheritdoc}
     */
    public static function getPublicNames() : array {
        return [
            'tasks:createNew',
            'tasks:getOne',
            'tasks:updateOne',
            'tasks:listAll',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function register(App $app) {
        $app->getContainer()[\App\Controller\Profile\Tasks::class] = function (ContainerInterface $container) {
            return new \App\Controller\Profile\Tasks(
                $container->get('repositoryFactory')->create('Profile\Task'),
                $container->get('repositoryFactory')->create('Profile\Process'),
                $container->get('commandBus'),
                $container->get('commandFactory')
            );
        };

        $container            = $app->getContainer();
        $authMiddleware       = $container->get('authMiddleware');
        $permissionMiddleware = $container->get('endpointPermissionMiddleware');

        self::createNew($app, $authMiddleware, $permissionMiddleware);
        self::getOne($app, $authMiddleware, $permissionMiddleware);
        self::updateOne($app, $authMiddleware, $permissionMiddleware);
        self::listAll($app, $authMiddleware, $permissionMiddleware);
    }

    /**
     * Create new Task.
     *
     * Create a new task for the given process.
     *
     * @apiEndpoint POST /profiles/{userName}/processes//{processId}/tasks
     * @apiGroup Profile Tasks
     * @apiAuth header key credentialToken wqxehuwqwsthwosjbxwwsqwsdi A Valid Credential Token
     * @apiAuth query key credentialToken wqxehuwqwsthwosjbxwwsqwsdi A Valid Credential Token
     * @apiEndpointURIFragment string userName f67b96dcf96b49d713a520ce9f54053c
     * @apiEndpointURIFragment int processId 1325
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/profile/tasks/createNew.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Profile\Tasks::createNew
     */
    private static function createNew(App $app, callable $auth, callable $permission) {
        $app
            ->post(
                '/profiles/{userName:[a-zA-Z0-9_-]+}/processes/{processId:[0-9]+}/tasks',
                'App\Controller\Profile\Tasks:createNew'
            )
            ->add($permission(EndpointPermission::PRIVATE_ACTION))
            ->add($auth(Auth::CREDENTIAL))
            ->setName('tasks:createNew');
    }

    /**
     * Retrieve a single Task.
     *
     * Retrieves all public information from a Task.
     *
     * @apiEndpoint GET /profiles/{userName}/processes/{processId}/tasks/{taskId}
     * @apiGroup Profile Tasks
     * @apiAuth header key credentialToken wqxehuwqwsthwosjbxwwsqwsdi A Valid Credential Token
     * @apiAuth query key credentialToken wqxehuwqwsthwosjbxwwsqwsdi A Valid Credential Token
     * @apiEndpointURIFragment string userName f67b96dcf96b49d713a520ce9f54053c
     * @apiEndpointURIFragment int processId 1325
     * @apiEndpointURIFragment int taskId 1
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/profile/tasks/getOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Profile\Tasks::getOne
     */
    private static function getOne(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/profiles/{userName:[a-zA-Z0-9_-]+}/processes/{processId:[0-9]+}/tasks/{taskId:[0-9]+}',
                'App\Controller\Profile\Tasks:getOne'
            )
            ->add($permission(EndpointPermission::PRIVATE_ACTION))
            ->add($auth(Auth::CREDENTIAL))
            ->setName('tasks:getOne');
    }

    /**
     * Update a single Task.
     *
     * Updates Task's specific information.
     *
     * @apiEndpoint PUT /profiles/{userName}/processes/{processId}/tasks/{taskId}
     * @apiGroup Profile Tasks
     * @apiAuth header key credentialToken wqxehuwqwsthwosjbxwwsqwsdi A Valid Credential Token
     * @apiAuth query key credentialToken wqxehuwqwsthwosjbxwwsqwsdi A Valid Credential Token
     * @apiEndpointURIFragment string userName f67b96dcf96b49d713a520ce9f54053c
     * @apiEndpointURIFragment int processId 1325
     * @apiEndpointURIFragment int taskId 1
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/profile/tasks/updateOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Profile\Tasks::updateOne
     */
    private static function updateOne(App $app, callable $auth, callable $permission) {
        $app
            ->put(
                '/profiles/{userName:[a-zA-Z0-9_-]+}/processes/{processId:[0-9]+}/tasks/{taskId:[0-9]+}',
                'App\Controller\Profile\Tasks:updateOne'
            )
            ->add($permission(EndpointPermission::PRIVATE_ACTION))
            ->add($auth(Auth::CREDENTIAL))
            ->setName('tasks:updateOne');
    }

    /**
     * List all tasks.
     *
     * Retrieve a complete list of all tasks that belong to the given user.
     *
     * @apiEndpoint GET /profiles/{userName}/processes/{processId}/tasks
     * @apiGroup Profile Tasks
     * @apiAuth header key credentialToken wqxehuwqwsthwosjbxwwsqwsdi A Valid Credential Token
     * @apiAuth query key credentialToken wqxehuwqwsthwosjbxwwsqwsdi A Valid Credential Token
     * @apiEndpointURIFragment string userName f67b96dcf96b49d713a520ce9f54053c
     * @apiEndpointURIFragment int processId 1325
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/profile/tasks/listAll.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Profile\Tasks::listAll
     */
    private static function listAll(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/profiles/{userName:[a-zA-Z0-9_-]+}/processes/{processId:[0-9]+}/tasks',
                'App\Controller\Profile\Tasks:listAll'
            )
            ->add($permission(EndpointPermission::PRIVATE_ACTION))
            ->add($auth(Auth::CREDENTIAL))
            ->setName('tasks:listAll');
    }
}
