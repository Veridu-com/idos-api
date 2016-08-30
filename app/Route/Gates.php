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
 * Gates routing definitions.
 *
 * @link docs/profile/gates/overview.md
 * @see App\Controller\Gates
 */
class Gates implements RouteInterface {
    /**
     * {@inheritdoc}
     */
    public static function getPublicNames() : array {
        return [
            'gates:listAll',
            'gates:deleteAll',
            'gates:createNew',
            'gates:getOne',
            'gates:updateOne',
            'gates:deleteOne'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function register(App $app) {
        $app->getContainer()[\App\Controller\Gates::class] = function (ContainerInterface $container) {
            return new \App\Controller\Gates(
                $container->get('repositoryFactory')->create('Gate'),
                $container->get('repositoryFactory')->create('User'),
                $container->get('commandBus'),
                $container->get('commandFactory')
            );
        };

        $container            = $app->getContainer();
        $authMiddleware       = $container->get('authMiddleware');
        $permissionMiddleware = $container->get('endpointPermissionMiddleware');

        self::listAll($app, $authMiddleware, $permissionMiddleware);
        self::deleteAll($app, $authMiddleware, $permissionMiddleware);
        self::createNew($app, $authMiddleware, $permissionMiddleware);
        self::getOne($app, $authMiddleware, $permissionMiddleware);
        self::updateOne($app, $authMiddleware, $permissionMiddleware);
        self::deleteOne($app, $authMiddleware, $permissionMiddleware);
    }

    /**
     * List all Gates.
     *
     * Retrieve a complete list of all gates that belong to the given user.
     *
     * @apiEndpoint GET /profiles/{userName}/gates
     * @apiGroup Profile Gates
     * @apiAuth header key credentialToken 2f476be4f457ef606f3b9177b5bf19c9 Company's credential token
     * @apiAuth query key credentialToken 2f476be4f457ef606f3b9177b5bf19c9 Company's credential token
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/profile/gates/listAll.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Gates::listAll
     */
    private static function listAll(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/profiles/{userName:[a-zA-Z0-9]+}/gates',
                'App\Controller\Gates:listAll'
            )
            ->add($permission(EndpointPermission::PRIVATE_ACTION))
            ->add($auth(Auth::CRED_TOKEN))
            ->setName('gates:listAll');
    }

    /**
     * Create new Gate.
     *
     * Create a new gate for the given user.
     *
     * @apiEndpoint POST profiles/{userName}/gates
     * @apiGroup Profile Gates
     * @apiAuth header key credentialToken 2f476be4f457ef606f3b9177b5bf19c9 Company's credential token
     * @apiAuth query key credentialToken 2f476be4f457ef606f3b9177b5bf19c9 Company's credential token
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/profile/gates/createNew.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Gates::createNew
     */
    private static function createNew(App $app, callable $auth, callable $permission) {
        $app
            ->post(
                '/profiles/{userName:[a-zA-Z0-9]+}/gates',
                'App\Controller\Gates:createNew'
            )
            ->add($permission(EndpointPermission::PRIVATE_ACTION))
            ->add($auth(Auth::CRED_TOKEN))
            ->setName('gates:createNew');
    }

    /**
     * Deletes a single Gate.
     *
     * Deletes a single Gate that belongs to the given user
     *
     * @apiEndpoint DELETE /profiles/{userName}/gates/{gateSlug}
     * @apiGroup Profile Gates
     * @apiAuth header key credentialToken 2f476be4f457ef606f3b9177b5bf19c9 Company's credential token
     * @apiAuth query key credentialToken 2f476be4f457ef606f3b9177b5bf19c9 Company's credential token
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/profile/gates/deleteOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Gates::deleteOne
     */
    private static function deleteOne(App $app, callable $auth, callable $permission) {
        $app
            ->delete(
                '/profiles/{userName:[a-zA-Z0-9_-]+}/gates/{gateSlug:[a-zA-Z0-9_-]+}',
                'App\Controller\Gates:deleteOne'
            )
            ->add($permission(EndpointPermission::PRIVATE_ACTION))
            ->add($auth(Auth::CRED_TOKEN))
            ->setName('gates:deleteOne');
    }

    /**
     * Deletes all gates.
     *
     * Deletes all gates that belongs to the given user
     *
     * @apiEndpoint DELETE /profiles/{userName}/gates
     * @apiGroup Profile Gates
     * @apiAuth header key credentialToken 2f476be4f457ef606f3b9177b5bf19c9 Company's credential token
     * @apiAuth query key credentialToken 2f476be4f457ef606f3b9177b5bf19c9 Company's credential token
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/profile/gates/deleteAll.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Gates::deleteAll
     */
    private static function deleteAll(App $app, callable $auth, callable $permission) {
        $app
            ->delete(
                '/profiles/{userName:[a-zA-Z0-9]+}/gates',
                'App\Controller\Gates:deleteAll'
            )
            ->add($permission(EndpointPermission::PRIVATE_ACTION))
            ->add($auth(Auth::CRED_TOKEN))
            ->setName('gates:deleteAll');
    }

    /**
     * Retrieve a single Gate.
     *
     * Retrieves all public information from a Gate.
     *
     * @apiEndpoint GET /profiles/{userName}/gates/{gateSlug}
     * @apiGroup Profile Gates
     * @apiAuth header key credentialToken 2f476be4f457ef606f3b9177b5bf19c9 Company's credential token
     * @apiAuth query key credentialToken 2f476be4f457ef606f3b9177b5bf19c9 Company's credential token
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/profile/gates/getOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Gates::getOne
     */
    private static function getOne(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/profiles/{userName:[a-zA-Z0-9]+}/gates/{gateSlug:[a-zA-Z0-9_-]+}',
                'App\Controller\Gates:getOne'
            )
            ->add($permission(EndpointPermission::PRIVATE_ACTION))
            ->add($auth(Auth::CRED_TOKEN))
            ->setName('gates:getOne');
    }

    /**
     * Update a single Gate.
     *
     * Updates Gate's specific information.
     *
     * @apiEndpoint PUT /profiles/{userName}/gates/{gateSlug}
     * @apiGroup Profile Gates
     * @apiAuth header key credentialToken 2f476be4f457ef606f3b9177b5bf19c9 Company's credential token
     * @apiAuth query key credentialToken 2f476be4f457ef606f3b9177b5bf19c9 Company's credential token
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/profile/gates/updateOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Gates::updateOne
     */
    private static function updateOne(App $app, callable $auth, callable $permission) {
        $app
            ->put(
                '/profiles/{userName:[a-zA-Z0-9_-]+}/gates/{gateSlug:[a-zA-Z0-9_-]+}',
                'App\Controller\Gates:updateOne'
            )
            ->add($permission(EndpointPermission::PRIVATE_ACTION))
            ->add($auth(Auth::CRED_TOKEN))
            ->setName('gates:updateOne');
    }
}
