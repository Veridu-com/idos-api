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
 * Tags routing definitions.
 *
 * @link docs/profiles/tags/overview.md
 * @see App\Controller\Tags
 */
class Tags implements RouteInterface {
    /**
     * {@inheritdoc}
     */
    public static function getPublicNames() : array {
        return [
            'tags:listAll',
            'tags:createNew',
            'tags:deleteAll',
            'tags:getOne',
            'tags:deleteOne'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function register(App $app) {
        $app->getContainer()[\App\Controller\Tags::class] = function (ContainerInterface $container) {
            return new \App\Controller\Tags(
                $container->get('repositoryFactory')->create('Tag'),
                $container->get('repositoryFactory')->create('User'),
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
        self::deleteOne($app, $authMiddleware, $permissionMiddleware);
    }

    /**
     * List all Tags.
     *
     * Retrieve a complete list of all tags that belong to the requesting user.
     *
     * @apiEndpoint GET /profiles/{userName}/tags
     * @apiGroup Profile Tags
     * @apiAuth header key credToken 2f476be4f457ef606f3b9177b5bf19c9 Credential's Token
     * @apiAuth header key credPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Credential's Private Key
     * @apiAuth query key credToken 2f476be4f457ef606f3b9177b5bf19c9 Credential's Token
     * @apiAuth query key credPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Credential's Private Key
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/profiles/tags/listAll.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Tags::listAll
     */
    private static function listAll(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/profiles/{userName:[a-zA-Z0-9]+}/tags',
                'App\Controller\Tags:listAll'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::COMPANY))
            ->setName('tags:listAll');
    }
    /**
     * Creates new Tag.
     *
     * Creates a new tag for the requesting user.
     *
     * @apiEndpoint POST /profiles/{userName}/tags
     * @apiGroup Profile Tags
     * @apiAuth header key credToken 2f476be4f457ef606f3b9177b5bf19c9 Credential's Token
     * @apiAuth header key credPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Credential's Private Key
     * @apiAuth query key credToken 2f476be4f457ef606f3b9177b5bf19c9 Credential's Token
     * @apiAuth query key credPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Credential's Private Key
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/profiles/tags/createNew.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Tags::createNew
     */
    private static function createNew(App $app, callable $auth, callable $permission) {
        $app
            ->post(
                '/profiles/{userName:[a-zA-Z0-9]+}/tags',
                'App\Controller\Tags:createNew'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::COMPANY))
            ->setName('tags:createNew');
    }

    /**
     * Delete All Tags.
     *
     * Delete all tags that belong to the requesting user.
     *
     * @apiEndpoint DELETE /profiles/{userName}/tags
     * @apiGroup Profile Tags
     * @apiAuth header key credToken 2f476be4f457ef606f3b9177b5bf19c9 Credential's Token
     * @apiAuth header key credPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Credential's Private Key
     * @apiAuth query key credToken 2f476be4f457ef606f3b9177b5bf19c9 Credential's Token
     * @apiAuth query key credPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Credential's Private Key
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/profiles/tags/deleteAll.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Tags::deleteAll
     */
    private static function deleteAll(App $app, callable $auth, callable $permission) {
        $app
            ->delete(
                '/profiles/{userName:[a-zA-Z0-9]+}/tags',
                'App\Controller\Tags:deleteAll'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::COMPANY))
            ->setName('tags:deleteAll');
    }

    /*
     * Retrieve a single Tag.
     *
     * Retrieves all public information from a Tag
     *
     * @apiEndpoint GET /profiles/{userName}/tags/{tagSlug}
     * @apiGroup Profile Tags
     * @apiAuth header key credToken 2f476be4f457ef606f3b9177b5bf19c9 Credential's Token
     * @apiAuth header key credPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Credential's Private Key
     * @apiAuth query key credToken 2f476be4f457ef606f3b9177b5bf19c9 Credential's Token
     * @apiAuth query key credPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Credential's Private Key
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     * @apiEndpointURIFragment string tagSlug tag-test
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/profiles/tags/getOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Tags::getOne
     */
    private static function getOne(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/profiles/{userName:[a-zA-Z0-9]+}/tags/{tagSlug}',
                'App\Controller\Tags:getOne'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::COMPANY))
            ->setName('tags:getOne');
    }

    /*
     * Deletes a single Tag.
     *
     * Deletes a single Tag that belongs to the requesting user.
     *
     * @apiEndpoint DELETE /profiles/{userName}/tags/{tagSlug}
     * @apiGroup Profile Tags
     * @apiAuth header key credToken 2f476be4f457ef606f3b9177b5bf19c9 Credential's Token
     * @apiAuth header key credPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Credential's Private Key
     * @apiAuth query key credToken 2f476be4f457ef606f3b9177b5bf19c9 Credential's Token
     * @apiAuth query key credPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Credential's Private Key
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     * @apiEndpointURIFragment string tagSlug tag-test
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/profiles/tags/deleteOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Tags::deleteOne
     */
    private static function deleteOne(App $app, callable $auth, callable $permission) {
        $app
            ->delete(
                '/profiles/{userName:[a-zA-Z0-9]+}/tags/{tagSlug}',
                'App\Controller\Tags:deleteOne'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::COMPANY))
            ->setName('tags:deleteOne');
    }
}
