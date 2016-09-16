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
 * Raw routing definitions.
 *
 * @link docs/profiles/sources/raw/overview.md
 * @see App\Controller\Raw
 */
class Raw implements RouteInterface {
    /**
     * {@inheritdoc}
     */
    public static function getPublicNames() : array {
        return [
            'raw:listAll',
            'raw:createNew',
            'raw:deleteAll',
            'raw:getOne',
            'raw:updateOne',
            'raw:deleteOne'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function register(App $app) {
        $app->getContainer()[\App\Controller\Raw::class] = function (ContainerInterface $container) {
            return new \App\Controller\Raw(
                $container->get('repositoryFactory')->create('Raw'),
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
        self::updateOne($app, $authMiddleware, $permissionMiddleware);
    }

    /**
     * List all raw source data.
     *
     * Retrieve a complete list of the raw data by a given source.
     *
     * @apiEndpoint GET /profiles/{userName}/raw
     * @apiGroup Sources Raw
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
     * @link docs/sources/raw/listAll.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Raw::listAll
     */
    private static function listAll(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/profiles/{userName:[a-zA-Z0-9_-]+}/raw',
                'App\Controller\Raw:listAll'
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
     * @apiGroup Sources Raw
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
     * @link docs/sources/raw/createNew.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Raw::createNew
     */
    private static function createNew(App $app, callable $auth, callable $permission) {
        $app
            ->post(
                '/profiles/{userName:[a-zA-Z0-9_-]+}/raw',
                'App\Controller\Raw:createNew'
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
     * @apiGroup Company Members
     * @apiAuth header key credentialToken 2f476be4f457ef606f3b9177b5bf19c9 Company's credential token
     * @apiAuth query key credentialToken 2f476be4f457ef606f3b9177b5bf19c9 Company's credential token
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     * @apiEndpointURIFragment int sourceId 12345
     * @apiEndpointURIFragment string collection data-name
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
            ->patch(
                '/profiles/{userName:[a-zA-Z0-9_-]+}/raw/{sourceId:[0-9]+}',
                'App\Controller\Raw:updateOne'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::CREDENTIAL))
            ->setName('raw:updateOne');
    }
}
