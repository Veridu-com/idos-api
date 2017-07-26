<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
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
 * Profile References.
 *
 * The Profile References endpoint allows you to confirm authenticity of information you already have for a user.
 * You can submit reference data for the user and retrieve information on how well this matches to data processed
 * from other sources.
 *
 * @link docs/profiles/reference/overview.md
 * @see \App\Controller\Profile\References
 */
class References implements RouteInterface {
    /**
     * {@inheritdoc}
     */
    public static function getPublicNames() : array {
        return [
            'reference:listAll',
            'reference:getOne',
            'reference:createNew',
            'reference:updateOne',
            'reference:deleteOne',
            'reference:deleteAll'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function register(App $app) : void {
        $app->getContainer()[\App\Controller\Profile\References::class] = function (ContainerInterface $container) : ControllerInterface {
            return new \App\Controller\Profile\References(
                $container
                    ->get('repositoryFactory')
                    ->create('Profile\Reference'),
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
     * List all reference data.
     *
     * Retrieve a complete list of the references by a given user.
     *
     * @apiEndpoint GET /profiles/{userName}/references
     * @apiGroup Profile
     * @apiAuth header token CredentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential Token
     * @apiAuth query token credentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential Token
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     *
     * @param \Slim\App $app
     * @param callable  $auth
     * @param callable  $permission
     *
     * @return void
     *
     * @link docs/sources/reference/listAll.md
     * @see \App\Middleware\Auth::__invoke
     * @see \App\Middleware\Permission::__invoke
     * @see \App\Controller\Profile\References::listAll
     */
    private static function listAll(App $app, callable $auth, callable $permission) : void {
        $app
            ->get(
                '/profiles/{userName:[a-zA-Z0-9_-]+}/references',
                'App\Controller\Profile\References:listAll'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::CREDENTIAL))
            ->setName('reference:listAll');
    }

    /**
     * Retrieves a reference.
     *
     * Retrieves a reference from the given user.
     *
     * @apiEndpoint GET /profiles/{userName}/references/{referenceName}
     * @apiGroup Profile
     * @apiAuth header token CredentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential Token
     * @apiAuth query token credentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential Token
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     * @apiEndpointURIFragment string referenceName data-name
     *
     * @param \Slim\App $app
     * @param callable  $auth
     * @param callable  $permission
     *
     * @return void
     *
     * @link docs/sources/reference/getOne.md
     * @see \App\Middleware\Auth::__invoke
     * @see \App\Middleware\Permission::__invoke
     * @see \App\Controller\Profile\References::getOne
     */
    private static function getOne(App $app, callable $auth, callable $permission) : void {
        $app
            ->get(
                '/profiles/{userName:[a-zA-Z0-9_-]+}/references/{referenceName:[a-zA-Z0-9_-]+}',
                'App\Controller\Profile\References:getOne'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::CREDENTIAL))
            ->setName('reference:getOne');
    }

    /**
     * Creates a new reference.
     *
     * Creates a new reference for the given user.
     *
     * @apiEndpoint POST /profiles/{userName}/references
     * @apiGroup Profile
     * @apiAuth header token CredentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential Token
     * @apiAuth query token credentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential Token
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     *
     * @param \Slim\App $app
     * @param callable  $auth
     * @param callable  $permission
     *
     * @return void
     *
     * @link docs/sources/reference/createNew.md
     * @see \App\Middleware\Auth::__invoke
     * @see \App\Middleware\Permission::__invoke
     * @see \App\Controller\Profile\References::createNew
     */
    private static function createNew(App $app, callable $auth, callable $permission) : void {
        $app
            ->post(
                '/profiles/{userName:[a-zA-Z0-9_-]+}/references',
                'App\Controller\Profile\References:createNew'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::CREDENTIAL))
            ->setName('reference:createNew');
    }

    /**
     * Update a reference.
     *
     * Updates a reference for the given user.
     *
     * @apiEndpoint PATCH /profiles/{userName}/references/{referenceName}
     * @apiGroup Profile
     * @apiAuth header token CredentialToken wqxehuwqwsthwosjbxwwsqwsdi A Valid Credential Token
     * @apiAuth query token credentialToken wqxehuwqwsthwosjbxwwsqwsdi A Valid Credential Token
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     * @apiEndpointURIFragment string referenceName reference-name
     *
     * @param \Slim\App $app
     * @param callable  $auth
     * @param callable  $permission
     *
     * @return void
     *
     * @link docs/management/members/updateOne.md
     * @see \App\Middleware\Auth::__invoke
     * @see \App\Middleware\Permission::__invoke
     * @see \App\Controller\Profile\References::updateOne
     */
    private static function updateOne(App $app, callable $auth, callable $permission) : void {
        $app
            ->patch(
                '/profiles/{userName:[a-zA-Z0-9_-]+}/references/{referenceName:[a-zA-Z0-9_-]+}',
                'App\Controller\Profile\References:updateOne'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::CREDENTIAL))
            ->setName('reference:updateOne');
    }

    /**
     * Deletes a reference.
     *
     * Deletes a reference from the given user.
     *
     * @apiEndpoint DELETE /profiles/{userName}/references/{referenceName}
     * @apiGroup Profile
     * @apiAuth header token CredentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential Token
     * @apiAuth query token credentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential Token
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     * @apiEndpointURIFragment string referenceName data-name
     *
     * @param \Slim\App $app
     * @param callable  $auth
     * @param callable  $permission
     *
     * @return void
     *
     * @link docs/sources/reference/deleteOne.md
     * @see \App\Middleware\Auth::__invoke
     * @see \App\Middleware\Permission::__invoke
     * @see \App\Controller\Profile\References::deleteOne
     */
    private static function deleteOne(App $app, callable $auth, callable $permission) : void {
        $app
            ->delete(
                '/profiles/{userName:[a-zA-Z0-9_-]+}/references/{referenceName:[a-zA-Z0-9_-]+}',
                'App\Controller\Profile\References:deleteOne'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::CREDENTIAL))
            ->setName('reference:deleteOne');
    }

    /**
     * Deletes all references.
     *
     * Deletes all references from the given user.
     *
     * @apiEndpoint DELETE /profiles/{userName}/references
     * @apiGroup Profile
     * @apiAuth header token CredentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential Token
     * @apiAuth query token credentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential Token
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     *
     * @param \Slim\App $app
     * @param callable  $auth
     * @param callable  $permission
     *
     * @return void
     *
     * @link docs/sources/reference/deleteAll.md
     * @see \App\Middleware\Auth::__invoke
     * @see \App\Middleware\Permission::__invoke
     * @see \App\Controller\Profile\References::deleteAll
     */
    private static function deleteAll(App $app, callable $auth, callable $permission) : void {
        $app
            ->delete(
                '/profiles/{userName:[a-zA-Z0-9_-]+}/references',
                'App\Controller\Profile\References:deleteAll'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::CREDENTIAL))
            ->setName('reference:deleteAll');
    }
}
