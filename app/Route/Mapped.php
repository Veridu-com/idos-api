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
 * Mapped routing definitions.
 *
 * @link docs/profiles/sources/mapped/overview.md
 * @see App\Controller\Mapped
 */
class Mapped implements RouteInterface {
    /**
     * {@inheritdoc}
     */
    public static function getPublicNames() : array {
        return [
            'mapped:listAll',
            'mapped:createNew',
            'mapped:deleteAll',
            'mapped:getOne',
            'mapped:updateOne',
            'mapped:deleteOne'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function register(App $app) {
        $app->getContainer()[\App\Controller\Mapped::class] = function (ContainerInterface $container) {
            return new \App\Controller\Mapped(
                $container->get('repositoryFactory')->create('Mapped'),
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
     * List all mapped source data.
     *
     * Retrieve a complete list of the data mapped by a given source.
     *
     * @apiEndpoint GET /profiles/{userName}/sources/{sourceId}/mapped
     * @apiGroup Sources Mapped
     * @apiAuth header key credToken 2f476be4f457ef606f3b9177b5bf19c9 Credential's Token
     * @apiAuth query key credToken 2f476be4f457ef606f3b9177b5bf19c9 Credential's Token
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     * @apiEndpointURIFragment int sourceId 12345
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/sources/mapped/listAll.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Mapped::listAll
     */
    private static function listAll(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/profiles/{userName:[a-zA-Z0-9]+}/sources/{sourceId:[0-9+]}/mapped',
                'App\Controller\Mapped:listAll'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::CRED_TOKEN))
            ->setName('mapped:listAll');
    }
    /**
     * Creates a new mapped source data.
     *
     * Creates a new mapped data for the given source.
     *
     * @apiEndpoint POST /profiles/{userName}/source/{sourceId}/mapped
     * @apiGroup Sources Mapped
     * @apiAuth header key credToken 2f476be4f457ef606f3b9177b5bf19c9 Credential's Token
     * @apiAuth query key credToken 2f476be4f457ef606f3b9177b5bf19c9 Credential's Token
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     * @apiEndpointURIFragment int sourceId 12345
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/sources/mapped/createNew.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Mapped::createNew
     */
    private static function createNew(App $app, callable $auth, callable $permission) {
        $app
            ->post(
                '/profiles/{userName:[a-zA-Z0-9]+}/sources/{sourceId:[0-9+]}/mapped',
                'App\Controller\Mapped:createNew'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::CRED_TOKEN))
            ->setName('mapped:createNew');
    }

    /**
     * Update a mapped data.
     *
     * Updates a mapped data in the given source.
     *
     * @apiEndpoint PUT /profiles/{userName}/source/{sourceId}/mapped/{mappedName}
     * @apiGroup Company Members
     * @apiAuth header key credentialToken 2f476be4f457ef606f3b9177b5bf19c9 Company's credential token
     * @apiAuth query key credentialToken 2f476be4f457ef606f3b9177b5bf19c9 Company's credential token
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     * @apiEndpointURIFragment int sourceId 12345
     * @apiEndpointURIFragment string mappedName data-name
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
                '/profiles/{userName:[a-zA-Z0-9]+}/sources/{sourceId:[0-9+]}/mapped/{mappedName}',
                'App\Controller\Mapped:updateOne'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::CRED_TOKEN))
            ->setName('mapped:updateOne');
    }

    /*
     * Retrieves a mapped data.
     *
     * Retrieves a mapped data from a given source.
     *
     * @apiEndpoint GET /profiles/{userName}/source/{sourceId}/mapped/{mappedName}
     * @apiGroup Sources Mapped
     * @apiAuth header key credToken 2f476be4f457ef606f3b9177b5bf19c9 Credential's Token
     * @apiAuth query key credToken 2f476be4f457ef606f3b9177b5bf19c9 Credential's Token
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     * @apiEndpointURIFragment int sourceId 12345
     * @apiEndpointURIFragment string mappedName data-name
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/sources/mapped/getOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Mapped::getOne
     */
    private static function getOne(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/profiles/{userName:[a-zA-Z0-9]+}/sources/{sourceId:[0-9+]}/mapped/{mappedName}',
                'App\Controller\Mapped:getOne'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::CRED_TOKEN))
            ->setName('mapped:getOne');
    }

    /**
     * Deletes all mapped data.
     *
     * Deletes all mapped data from the given source.
     *
     * @apiEndpoint DELETE /profiles/{userName}/source/{sourceId}/mapped
     * @apiGroup Sources Mapped
     * @apiAuth header key credToken 2f476be4f457ef606f3b9177b5bf19c9 Credential's Token
     * @apiAuth query key credToken 2f476be4f457ef606f3b9177b5bf19c9 Credential's Token
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     * @apiEndpointURIFragment int sourceId 12345
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/sources/mapped/deleteAll.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Mapped::deleteAll
     */
    private static function deleteAll(App $app, callable $auth, callable $permission) {
        $app
            ->delete(
                '/profiles/{userName:[a-zA-Z0-9]+}/sources/{sourceId:[0-9+]}/mapped',
                'App\Controller\Mapped:deleteAll'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::CRED_TOKEN))
            ->setName('mapped:deleteAll');
    }

    /*
     * Deletes a mapped data.
     *
     * Deletes a mapped data from the given source.
     *
     * @apiEndpoint DELETE /profiles/{userName}/source/{sourceId}/mapped/{mappedName}
     * @apiGroup Sources Mapped
     * @apiAuth header key credToken 2f476be4f457ef606f3b9177b5bf19c9 Credential's Token
     * @apiAuth query key credToken 2f476be4f457ef606f3b9177b5bf19c9 Credential's Token
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     * @apiEndpointURIFragment int sourceId 12345
     * @apiEndpointURIFragment string mappedName data-name
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/sources/mapped/deleteOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Mapped::deleteOne
     */
    private static function deleteOne(App $app, callable $auth, callable $permission) {
        $app
            ->delete(
                '/profiles/{userName:[a-zA-Z0-9]+}/sources/{sourceId:[0-9+]}/mapped/{mappedName}',
                'App\Controller\Mapped:deleteOne'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::CRED_TOKEN))
            ->setName('mapped:deleteOne');
    }
}
