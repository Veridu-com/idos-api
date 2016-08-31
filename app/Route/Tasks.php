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
 * Tasks routing definitions.
 *
 * @link docs/profile/tasks/overview.md
 * @see App\Controller\Tasks
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
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function register(App $app) {
        $app->getContainer()[\App\Controller\Tasks::class] = function (ContainerInterface $container) {
            return new \App\Controller\Tasks(
                $container->get('repositoryFactory')->create('Task'),
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
    }

    /**
     * Create new Task.
     *
     * Create a new task for the given process.
     *
     * @apiEndpoint POST profiles/{processId}/tasks
     * @apiGroup Profile Tasks
     * @apiAuth header key credentialToken 2f476be4f457ef606f3b9177b5bf19c9 Company's credential token
     * @apiAuth query key credentialToken 2f476be4f457ef606f3b9177b5bf19c9 Company's credential token
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/profile/tasks/createNew.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Tasks::createNew
     */
    private static function createNew(App $app, callable $auth, callable $permission) {
        $app
            ->post(
                '/profiles/{processId:[0-9]+}/tasks',
                'App\Controller\Tasks:createNew'
            )
            ->add($permission(EndpointPermission::PRIVATE_ACTION))
            ->add($auth(Auth::CRED_TOKEN))
            ->setName('tasks:createNew');
    }

    /**
     * Retrieve a single Task.
     *
     * Retrieves all public information from a Task.
     *
     * @apiEndpoint GET /profiles/{processId}/tasks/{taskId}
     * @apiGroup Profile Tasks
     * @apiAuth header key credentialToken 2f476be4f457ef606f3b9177b5bf19c9 Company's credential token
     * @apiAuth query key credentialToken 2f476be4f457ef606f3b9177b5bf19c9 Company's credential token
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/profile/tasks/getOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Tasks::getOne
     */
    private static function getOne(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/profiles/{processId:[0-9]+}/tasks/{taskId:[0-9]+}',
                'App\Controller\Tasks:getOne'
            )
            ->add($permission(EndpointPermission::PRIVATE_ACTION))
            ->add($auth(Auth::CRED_TOKEN))
            ->setName('tasks:getOne');
    }

    /**
     * Update a single Task.
     *
     * Updates Task's specific information.
     *
     * @apiEndpoint PUT /profiles/{processId}/tasks/{taskId}
     * @apiGroup Profile Tasks
     * @apiAuth header key credentialToken 2f476be4f457ef606f3b9177b5bf19c9 Company's credential token
     * @apiAuth query key credentialToken 2f476be4f457ef606f3b9177b5bf19c9 Company's credential token
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/profile/tasks/updateOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Tasks::updateOne
     */
    private static function updateOne(App $app, callable $auth, callable $permission) {
        $app
            ->put(
                '/profiles/{processId:[a-zA-Z0-9_-]+}/tasks/{taskId:[0-9]+}',
                'App\Controller\Tasks:updateOne'
            )
            ->add($permission(EndpointPermission::PRIVATE_ACTION))
            ->add($auth(Auth::CRED_TOKEN))
            ->setName('tasks:updateOne');
    }
}
