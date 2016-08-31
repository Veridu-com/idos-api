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
 * Digested routing definitions.
 *
 * @link docs/profiles/sources/digested/overview.md
 * @see App\Controller\Digested
 */
class Digested implements RouteInterface {
    /**
     * {@inheritdoc}
     */
    public static function getPublicNames() : array {
        return [
            'digested:listAll',
            'digested:createNew',
            'digested:deleteAll',
            'digested:getOne',
            'digested:updateOne',
            'digested:deleteOne'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function register(App $app) {
        $app->getContainer()[\App\Controller\Digested::class] = function (ContainerInterface $container) {
            return new \App\Controller\Digested(
                $container->get('repositoryFactory')->create('Digested'),
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
     * List all digested source data.
     *
     * Retrieve a complete list of the data digested by a given source.
     *
     * @apiEndpoint GET /profiles/{userName}/sources/{sourceId}/digested
     * @apiGroup Sources Digested
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
     * @link docs/sources/digested/listAll.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Digested::listAll
     */
    private static function listAll(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/profiles/{userName:[a-zA-Z0-9]+}/sources/{sourceId:[0-9+]}/digested',
                'App\Controller\Digested:listAll'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::CREDENTIAL))
            ->setName('digested:listAll');
    }
    /**
     * Creates a new digested source data.
     *
     * Creates a new digested data for the given source.
     *
     * @apiEndpoint POST /profiles/{userName}/source/{sourceId}/digested
     * @apiGroup Sources Digested
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
     * @link docs/sources/digested/createNew.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Digested::createNew
     */
    private static function createNew(App $app, callable $auth, callable $permission) {
        $app
            ->post(
                '/profiles/{userName:[a-zA-Z0-9]+}/sources/{sourceId:[0-9+]}/digested',
                'App\Controller\Digested:createNew'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::CREDENTIAL))
            ->setName('digested:createNew');
    }

    /**
     * Update a digested data.
     *
     * Updates a digested data in the given source.
     *
     * @apiEndpoint PUT /profiles/{userName}/source/{sourceId}/digested/{digestedName}
     * @apiGroup Company Members
     * @apiAuth header key credentialToken 2f476be4f457ef606f3b9177b5bf19c9 Company's credential token
     * @apiAuth query key credentialToken 2f476be4f457ef606f3b9177b5bf19c9 Company's credential token
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     * @apiEndpointURIFragment int sourceId 12345
     * @apiEndpointURIFragment string digestedName data-name
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
                '/profiles/{userName:[a-zA-Z0-9]+}/sources/{sourceId:[0-9+]}/digested/{digestedName:[a-zA-Z0-9]+}',
                'App\Controller\Digested:updateOne'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::CREDENTIAL))
            ->setName('digested:updateOne');
    }

    /*
     * Retrieves a digested data.
     *
     * Retrieves a digested data from a given source.
     *
     * @apiEndpoint GET /profiles/{userName}/source/{sourceId}/digested/{digestedName}
     * @apiGroup Sources Digested
     * @apiAuth header key credToken 2f476be4f457ef606f3b9177b5bf19c9 Credential's Token
     * @apiAuth query key credToken 2f476be4f457ef606f3b9177b5bf19c9 Credential's Token
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     * @apiEndpointURIFragment int sourceId 12345
     * @apiEndpointURIFragment string digestedName data-name
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/sources/digested/getOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Digested::getOne
     */
    private static function getOne(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/profiles/{userName:[a-zA-Z0-9]+}/sources/{sourceId:[0-9+]}/digested/{digestedName:[a-zA-Z0-9]+}',
                'App\Controller\Digested:getOne'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::CREDENTIAL))
            ->setName('digested:getOne');
    }

    /**
     * Deletes all digested data.
     *
     * Deletes all digested data from the given source.
     *
     * @apiEndpoint DELETE /profiles/{userName}/source/{sourceId}/digested
     * @apiGroup Sources Digested
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
     * @link docs/sources/digested/deleteAll.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Digested::deleteAll
     */
    private static function deleteAll(App $app, callable $auth, callable $permission) {
        $app
            ->delete(
                '/profiles/{userName:[a-zA-Z0-9]+}/sources/{sourceId:[0-9+]}/digested',
                'App\Controller\Digested:deleteAll'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::CREDENTIAL))
            ->setName('digested:deleteAll');
    }

    /*
     * Deletes a digested data.
     *
     * Deletes a digested data from the given source.
     *
     * @apiEndpoint DELETE /profiles/{userName}/source/{sourceId}/digested/{digestedName}
     * @apiGroup Sources Digested
     * @apiAuth header key credToken 2f476be4f457ef606f3b9177b5bf19c9 Credential's Token
     * @apiAuth query key credToken 2f476be4f457ef606f3b9177b5bf19c9 Credential's Token
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     * @apiEndpointURIFragment int sourceId 12345
     * @apiEndpointURIFragment string digestedName data-name
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/sources/digested/deleteOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Digested::deleteOne
     */
    private static function deleteOne(App $app, callable $auth, callable $permission) {
        $app
            ->delete(
                '/profiles/{userName:[a-zA-Z0-9]+}/sources/{sourceId:[0-9+]}/digested/{digestedName:[a-zA-Z0-9]+}',
                'App\Controller\Digested:deleteOne'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::CREDENTIAL))
            ->setName('digested:deleteOne');
    }
}
