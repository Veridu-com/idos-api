<?php
/*
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
 * Profile Sources.
 *
 * A Profile Source is the online platform from which the API is accessing a user's information (eg. Facebook,
 * Twitter, LinkedIn, etc.). You can associate one or more Profile Sources to each user.
 *
 * @link docs/profiles/sources/overview.md
 * @see \App\Controller\Profile\Sources
 */
class Sources implements RouteInterface {
    /**
     * {@inheritdoc}
     */
    public static function getPublicNames() : array {
        return [
            'sources:listAll',
            'sources:getOne',
            'sources:createNew',
            'sources:updateOne',
            'sources:deleteOne',
            'sources:deleteAll'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function register(App $app) : void {
        $app->getContainer()[\App\Controller\Profile\Sources::class] = function (ContainerInterface $container) : ControllerInterface {
            return new \App\Controller\Profile\Sources(
                $container
                    ->get('repositoryFactory')
                    ->create('Profile\Source'),
                $container
                    ->get('commandBus'),
                $container
                    ->get('commandFactory')
            );
        };

        $container            = $app->getContainer();
        $authMiddleware       = $container->get('authMiddleware');
        $permissionMiddleware = $container->get('endpointPermissionMiddleware');

        self::listAll($app, $authMiddleware, $permissionMiddleware);
        self::getOne($app, $authMiddleware, $permissionMiddleware);
        self::createNew($app, $authMiddleware, $permissionMiddleware);
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
     * @apiGroup Profile
     * @apiAuth header token UserToken|CredentialToken wqxehuwqwsthwosjbxwwsqwsdi A Valid User's|Credential's Token
     * @apiAuth query token userToken|credentialToken wqxehuwqwsthwosjbxwwsqwsdi A Valid User's|Credential's Token
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     *
     * @param \Slim\App $app
     * @param callable  $auth
     * @param callable  $permission
     *
     * @return void
     *
     * @link docs/profiles/sources/listAll.md
     * @see \App\Middleware\Auth::__invoke
     * @see \App\Middleware\Permission::__invoke
     * @see \App\Controller\Profile\Sources::listAll
     */
    private static function listAll(App $app, callable $auth, callable $permission) : void {
        $app
            ->get(
                '/profiles/{userName:[a-zA-Z0-9_-]+}/sources',
                'App\Controller\Profile\Sources:listAll'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::CREDENTIAL | Auth::USER))
            ->setName('sources:listAll');
    }

    /**
     * Retrieve a single Source.
     *
     * Retrieves all information from a Source.
     *
     * @apiEndpoint GET /profiles/{userName}/sources/{sourceId}
     * @apiGroup Profile
     * @apiAuth header token UserToken|CredentialToken wqxehuwqwsthwosjbxwwsqwsdi A Valid User's|Credential's Token
     * @apiAuth query token userToken|credentialToken wqxehuwqwsthwosjbxwwsqwsdi A Valid User's|Credential's Token
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     * @apiEndpointURIFragment int sourceId 12345
     *
     * @param \Slim\App $app
     * @param callable  $auth
     * @param callable  $permission
     *
     * @return void
     *
     * @link docs/profiles/sources/deleteAll.md
     * @see \App\Middleware\Auth::__invoke
     * @see \App\Middleware\Permission::__invoke
     * @see \App\Controller\Profile\Sources::getOne
     */
    private static function getOne(App $app, callable $auth, callable $permission) : void {
        $app
            ->get(
                '/profiles/{userName:[a-zA-Z0-9_-]+}/sources/{sourceId:[0-9]+}',
                'App\Controller\Profile\Sources:getOne'
            )
            ->add($permission(EndpointPermission::PRIVATE_ACTION))
            ->add($auth(Auth::CREDENTIAL | Auth::USER))
            ->setName('sources:getOne');
    }

    /**
     * Create new Source.
     *
     * Creates a new source for the requesting user.
     *
     * @apiEndpoint POST /profiles/{userName}/sources
     * @apiGroup Profile
     * @apiAuth header token UserToken|CredentialToken wqxehuwqwsthwosjbxwwsqwsdi A Valid User's|Credential's Token
     * @apiAuth query token userToken|credentialToken wqxehuwqwsthwosjbxwwsqwsdi A Valid User's|Credential's Token
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     *
     * @param \Slim\App $app
     * @param callable  $auth
     * @param callable  $permission
     *
     * @return void
     *
     * @link docs/profiles/sources/createNew.md
     * @see \App\Middleware\Auth::__invoke
     * @see \App\Middleware\Permission::__invoke
     * @see \App\Controller\Profile\Sources::createNew
     */
    private static function createNew(App $app, callable $auth, callable $permission) : void {
        $app
            ->post(
                '/profiles/{userName:[a-zA-Z0-9_-]+}/sources',
                'App\Controller\Profile\Sources:createNew'
            )
            ->add($permission(EndpointPermission::PRIVATE_ACTION))
            ->add($auth(Auth::CREDENTIAL | Auth::USER))
            ->setName('sources:createNew');
    }

    /**
     * Update a single Source.
     *
     * Updates a Source's specific information.
     *
     * @apiEndpoint PATCH /profiles/{userName}/sources/{sourceId}
     * @apiGroup Profile
     * @apiAuth header token UserToken|CredentialToken wqxehuwqwsthwosjbxwwsqwsdi A Valid User's|Credential's Token
     * @apiAuth query token userToken|credentialToken wqxehuwqwsthwosjbxwwsqwsdi A Valid User's|Credential's Token
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     * @apiEndpointURIFragment int sourceId 12345
     *
     * @param \Slim\App $app
     * @param callable  $auth
     * @param callable  $permission
     *
     * @return void
     *
     * @link docs/profiles/sources/updateOne.md
     * @see \App\Middleware\Auth::__invoke
     * @see \App\Middleware\Permission::__invoke
     * @see \App\Controller\Profile\Sources::updateOne
     */
    private static function updateOne(App $app, callable $auth, callable $permission) : void {
        $app
            ->patch(
                '/profiles/{userName:[a-zA-Z0-9_-]+}/sources/{sourceId:[0-9]+}',
                'App\Controller\Profile\Sources:updateOne'
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
     * @apiGroup Profile
     * @apiAuth header token UserToken|CredentialToken wqxehuwqwsthwosjbxwwsqwsdi A Valid User's|Credential's Token
     * @apiAuth query token userToken|credentialToken wqxehuwqwsthwosjbxwwsqwsdi A Valid User's|Credential's Token
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     * @apiEndpointURIFragment int sourceId 12345
     *
     * @param \Slim\App $app
     * @param callable  $auth
     * @param callable  $permission
     *
     * @return void
     *
     * @link docs/profiles/sources/deleteOne.md
     * @see \App\Middleware\Auth::__invoke
     * @see \App\Middleware\Permission::__invoke
     * @see \App\Controller\Profile\Sources::deleteOne
     */
    private static function deleteOne(App $app, callable $auth, callable $permission) : void {
        $app
            ->delete(
                '/profiles/{userName:[a-zA-Z0-9_-]+}/sources/{sourceId:[0-9]+}',
                'App\Controller\Profile\Sources:deleteOne'
            )
            ->add($permission(EndpointPermission::PRIVATE_ACTION))
            ->add($auth(Auth::CREDENTIAL | Auth::USER))
            ->setName('sources:deleteOne');
    }

    /**
     * Deletes all Sources.
     *
     * Delete all sources that belong to the target user.
     *
     * @apiEndpoint DELETE /profiles/{userName}/sources
     * @apiGroup Profile
     * @apiAuth header token UserToken|CredentialToken wqxehuwqwsthwosjbxwwsqwsdi A Valid User's|Credential's Token
     * @apiAuth query token userToken|credentialToken wqxehuwqwsthwosjbxwwsqwsdi A Valid User's|Credential's Token
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     *
     * @param \Slim\App $app
     * @param callable  $auth
     * @param callable  $permission
     *
     * @return void
     *
     * @link docs/profiles/sources/deleteAll.md
     * @see \App\Middleware\Auth::__invoke
     * @see \App\Middleware\Permission::__invoke
     * @see \App\Controller\Profile\Sources::deleteAll
     */
    private static function deleteAll(App $app, callable $auth, callable $permission) : void {
        $app
            ->delete(
                '/profiles/{userName:[a-zA-Z0-9_-]+}/sources',
                'App\Controller\Profile\Sources:deleteAll'
            )
            ->add($permission(EndpointPermission::PRIVATE_ACTION))
            ->add($auth(Auth::CREDENTIAL | Auth::USER))
            ->setName('sources:deleteAll');
    }
}
