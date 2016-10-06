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
 * Profile Raw is the raw data extracted from a Profile. This is what the API reads and extracts information in order to process more complex requests.
 *
 * @link docs/profiles/sources/raw/overview.md
 * @see App\Controller\Profile\Raw
 */
class Raw implements RouteInterface {
    /**
     * {@inheritdoc}
     */
    public static function getPublicNames() : array {
        return [
            'raw:listAll',
            'raw:createNew',
            'raw:updateOne',
            'raw:upsert'
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
        self::updateOne($app, $authMiddleware, $permissionMiddleware);
        self::upsert($app, $authMiddleware, $permissionMiddleware);
    }

    /**
     * List all raw source data.
     *
     * Retrieve a complete list of the raw data by a given source.
     *
     * @apiEndpoint GET /profiles/{userName}/raw
     * @apiGroup Profile Raw
     * @apiAuth header key CredentialToken  wqxehuwqwsthwosjbxwwsqwsdi A Valid Credential Token
     * @apiAuth query key credentialToken  wqxehuwqwsthwosjbxwwsqwsdi A Valid Credential Token
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/sources/raw/listAll.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Profile\Raw::listAll
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
     * @apiGroup Profile Raw
     * @apiAuth header key CredentialToken  wqxehuwqwsthwosjbxwwsqwsdi A Valid Credential Token
     * @apiAuth query key credentialToken  wqxehuwqwsthwosjbxwwsqwsdi A Valid Credential Token
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/sources/raw/createNew.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Profile\Raw::createNew
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
     * Update a raw data.
     *
     * Updates a raw data in the given source.
     *
     * @apiEndpoint PUT /profiles/{userName}/raw/{sourceId}
     * @apiGroup Profile Raw
     * @apiAuth header key CredentialToken  wqxehuwqwsthwosjbxwwsqwsdi A Valid Credential Token
     * @apiAuth query key credentialToken  wqxehuwqwsthwosjbxwwsqwsdi A Valid Credential Token
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     * @apiEndpointURIFragment int sourceId 12345
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
     * @see App\Controller\Company\Members::updateOne
     */
    private static function updateOne(App $app, callable $auth, callable $permission) {
        $app
            ->patch(
                '/profiles/{userName:[a-zA-Z0-9_-]+}/raw/{sourceId:[0-9]+}',
                'App\Controller\Profile\Raw:updateOne'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::CREDENTIAL))
            ->setName('raw:updateOne');
    }

    /**
     * Creates or updates raw data.
     *
     * Creates or update a raw data for the given source.
     *
     * @apiEndpoint POST /profiles/{userName}/raw
     * @apiGroup Profile Raw
     * @apiAuth header key CredentialToken  wqxehuwqwsthwosjbxwwsqwsdi A Valid Credential Token
     * @apiAuth query key credentialToken  wqxehuwqwsthwosjbxwwsqwsdi A Valid Credential Token
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/sources/raw/upsert.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Profile\Raw::upsert
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
