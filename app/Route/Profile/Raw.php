<?php
/*
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
 * Profile Raw.
 *
 * Profiles Raw is the raw data extracted from a Profile. This is what the API reads and extracts information in order
 * to process more complex requests.
 *
 * **Note:** advanced usage only.
 *
 * @link docs/profiles/sources/raw/overview.md
 * @see \App\Controller\Profile\Raw
 */
class Raw implements RouteInterface {
    /**
     * {@inheritdoc}
     */
    public static function getPublicNames() : array {
        return [
            'raw:listAll',
            'raw:createNew',
            'raw:upsert',
            'raw:deleteAll'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function register(App $app) {
        $app->getContainer()[\App\Controller\Profile\Raw::class] = function (ContainerInterface $container) {
            return new \App\Controller\Profile\Raw(
                $container->get('repositoryFactory')->create('Profile\Raw'),
                $container->get('repositoryFactory')->create('Profile\Source'),
                $container->get('commandBus'),
                $container->get('commandFactory')
            );
        };

        $container            = $app->getContainer();
        $authMiddleware       = $container->get('authMiddleware');
        $permissionMiddleware = $container->get('endpointPermissionMiddleware');

        self::listAll($app, $authMiddleware, $permissionMiddleware);
        self::createNew($app, $authMiddleware, $permissionMiddleware);
        self::upsert($app, $authMiddleware, $permissionMiddleware);
        self::deleteAll($app, $authMiddleware, $permissionMiddleware);
    }

    /**
     * List all raw source data.
     *
     * Retrieve a complete list of the raw data by a given source.
     *
     * @apiEndpoint GET /profiles/{userName}/raw
     * @apiGroup Profile
     * @apiAuth header token CredentialToken  wqxehuwqwsthwosjbxwwsqwsdi A Valid Credential Token
     * @apiAuth query token CredentialToken  wqxehuwqwsthwosjbxwwsqwsdi A Valid Credential Token
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/sources/raw/listAll.md
     * @see \App\Middleware\Auth::__invoke
     * @see \App\Middleware\Permission::__invoke
     * @see \App\Controller\Profile\Raw::listAll
     */
    private static function listAll(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/profiles/{userName:[a-zA-Z0-9_-]+}/raw',
                'App\Controller\Profile\Raw:listAll'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::CREDENTIAL))
            ->setName('raw:listAll');
    }

    /**
     * Creates a new raw data.
     *
     * Creates a new raw data for the given source.
     *
     * @apiEndpoint POST /profiles/{userName}/raw
     * @apiGroup Profile
     * @apiAuth header token CredentialToken  wqxehuwqwsthwosjbxwwsqwsdi A Valid Credential Token
     * @apiAuth query token CredentialToken  wqxehuwqwsthwosjbxwwsqwsdi A Valid Credential Token
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/sources/raw/createNew.md
     * @see \App\Middleware\Auth::__invoke
     * @see \App\Middleware\Permission::__invoke
     * @see \App\Controller\Profile\Raw::createNew
     */
    private static function createNew(App $app, callable $auth, callable $permission) {
        $app
            ->post(
                '/profiles/{userName:[a-zA-Z0-9_-]+}/raw',
                'App\Controller\Profile\Raw:createNew'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::CREDENTIAL))
            ->setName('raw:createNew');
    }

    /**
     * Deletes the raw data.
     *
     * Deletes the raw data of the given user.
     *
     * @apiEndpoint DELETE /profiles/{userName}/raw
     * @apiGroup Profile
     * @apiAuth header token CredentialToken  wqxehuwqwsthwosjbxwwsqwsdi A Valid Credential Token
     * @apiAuth query token CredentialToken  wqxehuwqwsthwosjbxwwsqwsdi A Valid Credential Token
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/sources/raw/deleteAll.md
     * @see \App\Middleware\Auth::__invoke
     * @see \App\Middleware\Permission::__invoke
     * @see \App\Controller\Profile\Raw::deleteAll
     */
    private static function deleteAll(App $app, callable $auth, callable $permission) {
        $app
            ->delete(
                '/profiles/{userName:[a-zA-Z0-9_-]+}/raw',
                'App\Controller\Profile\Raw:deleteAll'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::CREDENTIAL))
            ->setName('raw:deleteAll');
    }

    /**
     * Creates or updates raw data.
     *
     * Creates or update a raw data for the given source.
     *
     * @apiEndpoint POST /profiles/{userName}/raw
     * @apiGroup Profile
     * @apiAuth header token CredentialToken  wqxehuwqwsthwosjbxwwsqwsdi A Valid Credential Token
     * @apiAuth query token CredentialToken  wqxehuwqwsthwosjbxwwsqwsdi A Valid Credential Token
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/sources/raw/upsert.md
     * @see \App\Middleware\Auth::__invoke
     * @see \App\Middleware\Permission::__invoke
     * @see \App\Controller\Profile\Raw::upsert
     */
    private static function upsert(App $app, callable $auth, callable $permission) {
        $app
            ->put(
                '/profiles/{userName:[a-zA-Z0-9_-]+}/raw',
                'App\Controller\Profile\Raw:upsert'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::CREDENTIAL))
            ->setName('raw:upsert');
    }
}
