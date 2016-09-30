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
 * Profile Tags.
 *
 * A Profile tag is used by a Company to categorise and organise certain Profiles for the purposes of improving management of large numbers of Users and data. A Company will use Profile Tags in order to create specific categories of Profiles for Members to easily assign or view.
 *
 * @link docs/profiles/tags/overview.md
 * @see App\Controller\Profile\Tags
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
        $app->getContainer()[\App\Controller\Profile\Tags::class] = function (ContainerInterface $container) {
            return new \App\Controller\Profile\Tags(
                $container->get('repositoryFactory')->create('Profile\Tag'),
                $container->get('repositoryFactory')->create('User'),
                $container->get('commandBus'),
                $container->get('commandFactory')
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
     * @apiEndpoint GET /companies/{companySlug}/profiles/{userId}/tags
     * @apiGroup Profile Tags
     * @apiAuth header token CredentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential Token
     * @apiAuth query token credentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential Token
     * @apiEndpointURIFragment string companySlug veridu-ltd
     * @apiEndpointURIFragment int userId 1827452
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
     * @see App\Controller\Profile\Tags::listAll
     */
    private static function listAll(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/companies/{companySlug:[a-zA-Z0-9_-]+}/profiles/{userId:[0-9]+}/tags',
                'App\Controller\Profile\Tags:listAll'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::IDENTITY))
            ->setName('tags:listAll');
    }
    /**
     * Creates new Tag.
     *
     * Creates a new tag for the requesting user.
     *
     * @apiEndpoint POST /companies/{companySlug}/profiles/{userId}/tags
     * @apiGroup Profile Tags
     * @apiAuth header token CredentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential Token
     * @apiAuth query token credentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential Token
     * @apiEndpointURIFragment string companySlug veridu-ltd
     * @apiEndpointURIFragment int userId 1827452
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
     * @see App\Controller\Profile\Tags::createNew
     */
    private static function createNew(App $app, callable $auth, callable $permission) {
        $app
            ->post(
                '/companies/{companySlug:[a-zA-Z0-9_-]+}/profiles/{userId:[0-9]+}/tags',
                'App\Controller\Profile\Tags:createNew'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::IDENTITY))
            ->setName('tags:createNew');
    }

    /**
     * Delete All Tags.
     *
     * Delete all tags that belong to the requesting user.
     *
     * @apiEndpoint DELETE /companies/{companySlug}/profiles/{userId}/tags
     * @apiGroup Profile Tags
     * @apiAuth header token CredentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential Token
     * @apiAuth query token credentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential Token
     * @apiEndpointURIFragment string companySlug veridu-ltd
     * @apiEndpointURIFragment int userId 1827452
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
     * @see App\Controller\Profile\Tags::deleteAll
     */
    private static function deleteAll(App $app, callable $auth, callable $permission) {
        $app
            ->delete(
                '/companies/{companySlug:[a-zA-Z0-9_-]+}/profiles/{userId:[0-9]+}/tags',
                'App\Controller\Profile\Tags:deleteAll'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::IDENTITY))
            ->setName('tags:deleteAll');
    }

    /**
     * Retrieve a single Tag.
     *
     * Retrieves all public information from a Tag
     *
     * @apiEndpoint GET /companies/{companySlug}/profiles/{userId}/tags/{tagSlug}
     * @apiGroup Profile Tags
     * @apiAuth header token CredentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential Token
     * @apiAuth query token credentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential Token
     * @apiEndpointURIFragment string companySlug veridu-ltd
     * @apiEndpointURIFragment int userId 1827452
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
     * @see App\Controller\Profile\Tags::getOne
     */
    private static function getOne(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/companies/{companySlug:[a-zA-Z0-9_-]+}/profiles/{userId:[0-9]+}/tags/{tagSlug:[a-z0-9_-]+}',
                'App\Controller\Profile\Tags:getOne'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::IDENTITY))
            ->setName('tags:getOne');
    }

    /**
     * Deletes a single Tag.
     *
     * Deletes a single Tag that belongs to the requesting user.
     *
     * @apiEndpoint DELETE /companies/{companySlug}/profiles/{userId}/tags/{tagSlug}
     * @apiGroup Profile Tags
     * @apiAuth header token CredentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential Token
     * @apiAuth query token credentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential Token
     * @apiEndpointURIFragment string companySlug veridu-ltd
     * @apiEndpointURIFragment int userId 1827452
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
     * @see App\Controller\Profile\Tags::deleteOne
     */
    private static function deleteOne(App $app, callable $auth, callable $permission) {
        $app
            ->delete(
                '/companies/{companySlug:[a-zA-Z0-9_-]+}/profiles/{userId:[0-9]+}/tags/{tagSlug:[a-z0-9_-]+}',
                'App\Controller\Profile\Tags:deleteOne'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::IDENTITY))
            ->setName('tags:deleteOne');
    }
}
