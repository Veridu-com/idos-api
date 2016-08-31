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
 * Reference routing definitions.
 *
 * @link docs/profiles/reference/overview.md
 * @see App\Controller\Reference
 */
class References implements RouteInterface {
    /**
     * {@inheritdoc}
     */
    public static function getPublicNames() : array {
        return [
            'reference:listAll',
            'reference:createNew',
            'reference:deleteAll',
            'reference:getOne',
            'reference:updateOne',
            'reference:deleteOne'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function register(App $app) {
        $app->getContainer()[\App\Controller\References::class] = function (ContainerInterface $container) {
            return new \App\Controller\References(
                $container->get('repositoryFactory')->create('Reference'),
                $container->get('commandBus'),
                $container->get('commandFactory'),
                $container->get('optimus')
            );
        };

        $container            = $app->getContainer();
        $authMiddleware       = $container->get('authMiddleware');
        $permissionMiddleware = $container->get('endpointPermissionMiddleware');

        self::listAll($app, $authMiddleware, $permissionMiddleware);
        self::createNew($app, $authMiddleware, $permissionMiddleware);
        self::deleteAll($app, $authMiddleware, $permissionMiddleware);
        self::getOne($app, $authMiddleware, $permissionMiddleware);
        self::updateOne($app, $authMiddleware, $permissionMiddleware);
        self::deleteOne($app, $authMiddleware, $permissionMiddleware);
    }

    /**
     * List all reference data.
     *
     * Retrieve a complete list of the references by a given user.
     *
     * @apiEndpoint GET /profiles/{userName}/references
     * @apiGroup Sources Reference
     * @apiAuth header key credToken 2f476be4f457ef606f3b9177b5bf19c9 Credential's Token
     * @apiAuth query key credToken 2f476be4f457ef606f3b9177b5bf19c9 Credential's Token
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/sources/reference/listAll.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Reference::listAll
     */
    private static function listAll(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/profiles/{userName:[a-zA-Z0-9]+}/references',
                'App\Controller\References:listAll'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::CREDENTIAL))
            ->setName('reference:listAll');
    }
    /**
     * Creates a new reference.
     *
     * Creates a new reference for the given user.
     *
     * @apiEndpoint POST /profiles/{userName}/references
     * @apiGroup Sources Reference
     * @apiAuth header key credToken 2f476be4f457ef606f3b9177b5bf19c9 Credential's Token
     * @apiAuth query key credToken 2f476be4f457ef606f3b9177b5bf19c9 Credential's Token
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/sources/reference/createNew.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Reference::createNew
     */
    private static function createNew(App $app, callable $auth, callable $permission) {
        $app
            ->post(
                '/profiles/{userName:[a-zA-Z0-9]+}/references',
                'App\Controller\References:createNew'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::CREDENTIAL))
            ->setName('reference:createNew');
    }

    /**
     * Update an reference.
     *
     * Updates an reference for the given user.
     *
     * @apiEndpoint PUT /profiles/{userName}/references/{referenceName}
     * @apiGroup Profile References
     * @apiAuth header key credentialToken 2f476be4f457ef606f3b9177b5bf19c9 Company's credential token
     * @apiAuth query key credentialToken 2f476be4f457ef606f3b9177b5bf19c9 Company's credential token
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     * @apiEndpointURIFragment string referenceName data-name
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/management/members/updateOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Members::updateOne
     */
    private static function updateOne(App $app, callable $auth, callable $permission) {
        $app
            ->put(
                '/profiles/{userName:[a-zA-Z0-9]+}/references/{referenceName}',
                'App\Controller\References:updateOne'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::CREDENTIAL))
            ->setName('reference:updateOne');
    }

    /*
     * Retrieves an reference.
     *
     * Retrieves an reference from the given user.
     *
     * @apiEndpoint GET /profiles/{userName}/references/{referenceName}
     * @apiGroup Sources Reference
     * @apiAuth header key credToken 2f476be4f457ef606f3b9177b5bf19c9 Credential's Token
     * @apiAuth query key credToken 2f476be4f457ef606f3b9177b5bf19c9 Credential's Token
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     * @apiEndpointURIFragment string referenceName data-name
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/sources/reference/getOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Reference::getOne
     */
    private static function getOne(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/profiles/{userName:[a-zA-Z0-9]+}/references/{referenceName}',
                'App\Controller\References:getOne'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::CREDENTIAL))
            ->setName('reference:getOne');
    }

    /**
     * Deletes all references.
     *
     * Deletes all references from the given user.
     *
     * @apiEndpoint DELETE /profiles/{userName}/references
     * @apiGroup Sources Reference
     * @apiAuth header key credToken 2f476be4f457ef606f3b9177b5bf19c9 Credential's Token
     * @apiAuth query key credToken 2f476be4f457ef606f3b9177b5bf19c9 Credential's Token
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/sources/reference/deleteAll.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Reference::deleteAll
     */
    private static function deleteAll(App $app, callable $auth, callable $permission) {
        $app
            ->delete(
                '/profiles/{userName:[a-zA-Z0-9]+}/references',
                'App\Controller\References:deleteAll'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::CREDENTIAL))
            ->setName('reference:deleteAll');
    }

    /*
     * Deletes an reference.
     *
     * Deletes an reference from the given user.
     *
     * @apiEndpoint DELETE /profiles/{userName}/references/{referenceName}
     * @apiGroup Sources Reference
     * @apiAuth header key credToken 2f476be4f457ef606f3b9177b5bf19c9 Credential's Token
     * @apiAuth query key credToken 2f476be4f457ef606f3b9177b5bf19c9 Credential's Token
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     * @apiEndpointURIFragment string referenceName data-name
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/sources/reference/deleteOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Reference::deleteOne
     */
    private static function deleteOne(App $app, callable $auth, callable $permission) {
        $app
            ->delete(
                '/profiles/{userName:[a-zA-Z0-9]+}/references/{referenceName}',
                'App\Controller\References:deleteOne'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::CREDENTIAL))
            ->setName('reference:deleteOne');
    }
}
