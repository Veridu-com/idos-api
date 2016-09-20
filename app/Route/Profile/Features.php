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
 * Features routing definitions.
 *
 * @link docs/profile/features/overview.md
 * @see App\Controller\Profile\Features
 */
class Features implements RouteInterface {
    /**
     * {@inheritdoc}
     */
    public static function getPublicNames() : array {
        return [
            'features:listAll',
            'features:deleteAll',
            'features:createNew',
            'features:getOne',
            'features:updateOne',
            'features:upsert',
            'features:deleteOne'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function register(App $app) {
        $app->getContainer()[\App\Controller\Profile\Features::class] = function (ContainerInterface $container) {
            return new \App\Controller\Profile\Features(
                $container->get('repositoryFactory')->create('Profile\Feature'),
                $container->get('repositoryFactory')->create('User'),
                $container->get('repositoryFactory')->create('Source'),
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
        self::upsert($app, $authMiddleware, $permissionMiddleware);
        self::deleteOne($app, $authMiddleware, $permissionMiddleware);
    }

    /**
     * List all Features.
     *
     * Retrieve a complete list of all features that belong to the given user.
     *
     * @apiEndpoint GET /profiles/{userName}/features
     * @apiGroup Profile Features
     * @apiAuth header token CredentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential Token
     * @apiAuth query token credentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential Token
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/profile/features/listAll.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Profile\Features::listAll
     */
    private static function listAll(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/profiles/{userName:[a-zA-Z0-9_-]+}/features',
                'App\Controller\Profile\Features:listAll'
            )
            ->add($permission(EndpointPermission::PRIVATE_ACTION))
            ->add($auth(Auth::CREDENTIAL))
            ->setName('features:listAll');
    }

    /**
     * Create new Feature.
     *
     * Create a new feature for the given user.
     *
     * @apiEndpoint POST profiles/{userName}/features
     * @apiGroup Profile Features
     * @apiAuth header token CredentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential Token
     * @apiAuth query token credentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential Token
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/profile/features/createNew.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Profile\Features::createNew
     */
    private static function createNew(App $app, callable $auth, callable $permission) {
        $app
            ->post(
                '/profiles/{userName:[a-zA-Z0-9_-]+}/features',
                'App\Controller\Profile\Features:createNew'
            )
            ->add($permission(EndpointPermission::PRIVATE_ACTION))
            ->add($auth(Auth::CREDENTIAL))
            ->setName('features:createNew');
    }

    /**
     * Deletes a single Feature.
     *
     * Deletes a single Feature that belongs to the given user
     *
     * @apiEndpoint DELETE /profiles/{userName}/features/{featureId}
     * @apiGroup Profile Features
     * @apiAuth header token CredentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential Token
     * @apiAuth query token credentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential Token
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/profile/features/deleteOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Profile\Features::deleteOne
     */
    private static function deleteOne(App $app, callable $auth, callable $permission) {
        $app
            ->delete(
                '/profiles/{userName:[a-zA-Z0-9_-]+}/features/{featureId:[0-9]+}',
                'App\Controller\Features:deleteOne'
            )
            ->add($permission(EndpointPermission::PRIVATE_ACTION))
            ->add($auth(Auth::CREDENTIAL))
            ->setName('features:deleteOne');
    }

    /**
     * Deletes all features.
     *
     * Deletes all features that belongs to the given user
     *
     * @apiEndpoint DELETE /profiles/{userName}/features
     * @apiGroup Profile Features
     * @apiAuth header token CredentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential Token
     * @apiAuth query token credentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential Token
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/profile/features/deleteAll.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Profile\Features::deleteAll
     */
    private static function deleteAll(App $app, callable $auth, callable $permission) {
        $app
            ->delete(
                '/profiles/{userName:[a-zA-Z0-9_-]+}/features',
                'App\Controller\Profile\Features:deleteAll'
            )
            ->add($permission(EndpointPermission::PRIVATE_ACTION))
            ->add($auth(Auth::CREDENTIAL))
            ->setName('features:deleteAll');
    }

    /**
     * Retrieve a single Feature.
     *
     * Retrieves all public information from a Feature.
     *
     * @apiEndpoint GET /profiles/{userName}/features/{featureId}
     * @apiGroup Profile Features
     * @apiAuth header token CredentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential Token
     * @apiAuth query token credentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential Token
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/profile/features/getOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Profile\Features::getOne
     */
    private static function getOne(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/profiles/{userName:[a-zA-Z0-9_-]+}/features/{featureId:[0-9]+}',
                'App\Controller\Features:getOne'
            )
            ->add($permission(EndpointPermission::PRIVATE_ACTION))
            ->add($auth(Auth::CREDENTIAL))
            ->setName('features:getOne');
    }

    /**
     * Update a single Feature.
     *
     * Updates Feature's specific information.
     *
     * @apiEndpoint PATCH /profiles/{userName}/features/{featureId}
     * @apiGroup Profile Features
     * @apiAuth header token CredentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential Token
     * @apiAuth query token credentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential Token
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/profile/features/updateOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Profile\Features::updateOne
     */
    private static function updateOne(App $app, callable $auth, callable $permission) {
        $app
            ->patch(
                '/profiles/{userName:[a-zA-Z0-9_-]+}/features/{featureId:[0-9]+}',
                'App\Controller\Features:updateOne'
            )
            ->add($permission(EndpointPermission::PRIVATE_ACTION))
            ->add($auth(Auth::CREDENTIAL))
            ->setName('features:updateOne');
    }

    /**
     * Create or update a feature.
     *
     * Create or update a feature for the given user.
     *
     * @apiEndpoint PUT profiles/{userName}/features
     * @apiGroup Profile Features
     * @apiAuth header token CredentialToken XXX A valid Credential Token
     * @apiAuth query token credentialToken XXX A valid Credential Token
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/profile/features/createNew.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Features::createNew
     */
    private static function upsert(App $app, callable $auth, callable $permission) {
        $app
            ->put(
                '/profiles/{userName:[a-zA-Z0-9_-]+}/features',
                'App\Controller\Features:upsert'
            )
            ->add($permission(EndpointPermission::PRIVATE_ACTION))
            ->add($auth(Auth::CREDENTIAL))
            ->setName('features:upsert');
    }
}
