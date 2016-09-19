<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Route;

use App\Controller\ControllerInterface;
use App\Middleware\Auth;
use App\Middleware\EndpointPermission;
use Interop\Container\ContainerInterface;
use Slim\App;

/**
 * Profile Source
 *
 * A Profile Source is the online platform from which the API is accessing a user’s information (eg. Facebook, Twitter, LinkedIn etc.)
 *
 * @link docs/profiles/sources/overview.md
 * @see App\Controller\Sources
 */
class Sources implements RouteInterface {
    /**
     * {@inheritdoc}
     */
    public static function getPublicNames() : array {
        return [
            'sources:listAll',
            'sources:createNew',
            'sources:getOne',
            'sources:updateOne',
            'sources:deleteOne',
            'sources:deleteAll'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function register(App $app) {
        $app->getContainer()[\App\Controller\Sources::class] = function (ContainerInterface $container) : ControllerInterface {
            return new \App\Controller\Sources(
                $container->get('repositoryFactory')->create('Source'),
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
        self::getOne($app, $authMiddleware, $permissionMiddleware);
        self::updateOne($app, $authMiddleware, $permissionMiddleware);
        self::deleteOne($app, $authMiddleware, $permissionMiddleware);
        self::deleteAll($app, $authMiddleware, $permissionMiddleware);
    }

    /**
     * List all Sources.
     *
     * Retrieves a complete list of all sources that belong to the requesting user.
     *
     * @apiEndpoint GET /profiles/{userName}/sources
     * @apiGroup Profile Source
     * @apiAuth header token userToken|credentialToken wqxehuwqwsthwosjbxwwsqwsdi A Valid User's|Credential's Token
     * @apiAuth query token userToken|credentialToken wqxehuwqwsthwosjbxwwsqwsdi A Valid User's|Credential's Token
     *
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/profiles/sources/listAll.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Sources::listAll
     */
    private static function listAll(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/profiles/{userName:[a-zA-Z0-9_-]+}/sources',
                'App\Controller\Sources:listAll'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::CREDENTIAL | Auth::USER))
            ->setName('sources:listAll');
    }

    /**
     * Create new Source.
     *
     * Creates a new source for the requesting user.
     *
     * @apiEndpoint POST /profiles/{userName}/sources
     * @apiGroup Profile Source
     * @apiAuth header token userToken|credentialToken wqxehuwqwsthwosjbxwwsqwsdi A Valid User's|Credential's Token
     * @apiAuth query token userToken|credentialToken wqxehuwqwsthwosjbxwwsqwsdi A Valid User's|Credential's Token
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/profiles/sources/createNew.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Sources::createNew
     */
    private static function createNew(App $app, callable $auth, callable $permission) {
        $app
            ->post(
                '/profiles/{userName:[a-zA-Z0-9_-]+}/sources',
                'App\Controller\Sources:createNew'
            )
            ->add($permission(EndpointPermission::PRIVATE_ACTION))
            ->add($auth(Auth::CREDENTIAL | Auth::USER))
            ->setName('sources:createNew');
    }

    /**
     * Deletes all Sources.
     *
     * Delete all sources that belong to the target user.
     *
     * @apiEndpoint DELETE /profiles/{userName}/sources
     * @apiGroup Profile Source
     * @apiAuth header token userToken|credentialToken wqxehuwqwsthwosjbxwwsqwsdi A Valid User's|Credential's Token
     * @apiAuth query token userToken|credentialToken wqxehuwqwsthwosjbxwwsqwsdi A Valid User's|Credential's Token
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/profiles/sources/deleteAll.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Sources::deleteAll
     */
    private static function deleteAll(App $app, callable $auth, callable $permission) {
        $app
            ->delete(
                '/profiles/{userName:[a-zA-Z0-9_-]+}/sources',
                'App\Controller\Sources:deleteAll'
            )
            ->add($permission(EndpointPermission::PRIVATE_ACTION))
            ->add($auth(Auth::CREDENTIAL | Auth::USER))
            ->setName('sources:deleteAll');
    }

    /**
     * Retrieve a single Source.
     *
     * Retrieves all information from a Source.
     *
     * @apiEndpoint GET /profiles/{userName/sources/{sourceId}
     * @apiGroup Profile Source
     * @apiAuth header token userToken|credentialToken wqxehuwqwsthwosjbxwwsqwsdi A Valid User's|Credential's Token
     * @apiAuth query token userToken|credentialToken wqxehuwqwsthwosjbxwwsqwsdi A Valid User's|Credential's Token
     * @apiEndpointURIFragment int sourceId 12345
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/profiles/sources/deleteAll.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Sources::getOne
     */
    private static function getOne(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/profiles/{userName:[a-zA-Z0-9_-]+}/sources/{sourceId:[0-9]+}',
                'App\Controller\Sources:getOne'
            )
            ->add($permission(EndpointPermission::PRIVATE_ACTION))
            ->add($auth(Auth::CREDENTIAL | Auth::USER))
            ->setName('sources:getOne');
    }

    /**
     * Update a single Source.
     *
     * Updates a Source's specific information.
     *
     * @apiEndpoint PUT /profiles/{userName}/sources/{sourceId}
     * @apiGroup Profile Source
     * @apiAuth header token userToken|credentialToken wqxehuwqwsthwosjbxwwsqwsdi A Valid User's|Credential's Token
     * @apiAuth query token userToken|credentialToken wqxehuwqwsthwosjbxwwsqwsdi A Valid User's|Credential's Token
     * @apiEndpointURIFragment int sourceId 12345
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/profiles/sources/deleteAll.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Sources::updateOne
     */
    private static function updateOne(App $app, callable $auth, callable $permission) {
        $app
            ->put(
                '/profiles/{userName:[a-zA-Z0-9_-]+}/sources/{sourceId:[0-9]+}',
                'App\Controller\Sources:updateOne'
            )
            ->add($permission(EndpointPermission::PRIVATE_ACTION))
            ->add($auth(Auth::CREDENTIAL | Auth::USER))
            ->setName('sources:updateOne');
    }

    /**
     * Deletes a single Source.
     *
     * Deletes a source from the target user.
     *
     * @apiEndpoint DELETE /profiles/{userName}/sources/{sourceId}
     * @apiGroup Profile Source
     * @apiAuth header token userToken|credentialToken wqxehuwqwsthwosjbxwwsqwsdi A Valid User's|Credential's Token
     * @apiAuth query token userToken|credentialToken wqxehuwqwsthwosjbxwwsqwsdi A Valid User's|Credential's Token
     * @apiEndpointURIFragment int sourceId 12345
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/profiles/sources/deleteAll.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Sources::deleteOne
     */
    private static function deleteOne(App $app, callable $auth, callable $permission) {
        $app
            ->delete(
                '/profiles/{userName:[a-zA-Z0-9_-]+}/sources/{sourceId:[0-9]+}',
                'App\Controller\Sources:deleteOne'
            )
            ->add($permission(EndpointPermission::PRIVATE_ACTION))
            ->add($auth(Auth::CREDENTIAL | Auth::USER))
            ->setName('sources:deleteOne');
    }
}
