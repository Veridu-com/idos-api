<?php

/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Route;

use App\Middleware\Auth;
use App\Middleware\CompanyPermission;
use Interop\Container\ContainerInterface;
use Slim\App;

/**
 * Tags routing definitions.
 *
 * @link docs/companies/tags/overview.md
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
            'tags:updateOne',
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
        $permissionMiddleware = $container->get('companyPermissionMiddleware');

        self::listAll($app, $authMiddleware, $permissionMiddleware);
        self::createNew($app, $authMiddleware, $permissionMiddleware);
        self::deleteAll($app, $authMiddleware, $permissionMiddleware);
        self::getOne($app, $authMiddleware, $permissionMiddleware);
        self::updateOne($app, $authMiddleware, $permissionMiddleware);
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
            ->add($permission(CompanyPermission::PUBLIC_ACTION))
            ->add($auth(Auth::CRED_TOKEN | Auth::CRED_PRIVKEY))
            ->setName('tags:listAll');
    }
    /**
     * Creates new Tag.
     *
     * Creates a new member for the requesting company.
     *
     * @apiEndpoint POST /companies/{companySlug}/tags
     * @apiGroup Company Tags
     * @apiAuth header key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiAuth query key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiEndpointURIFragment string companySlug veridu-ltd
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/companies/tags/createNew.md
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
            ->add($permission(CompanyPermission::PUBLIC_ACTION))
            ->add($auth(Auth::CRED_TOKEN | Auth::CRED_PRIVKEY))
            ->setName('tags:createNew');
    }

    /**
     * Update a single Tag.
     *
     * Updates Tag's role
     *
     * @apiEndpoint PUT /companies/{companySlug}/tags/{userName}
     * @apiGroup Company Tags
     * @apiAuth header key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiAuth query key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiEndpointURIFragment string companySlug veridu-ltd
     * @apiEndpointURIFragment string userName johndoe
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/companies/tags/updateOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Tags::updateOne
     */
    private static function updateOne(App $app, callable $auth, callable $permission) {
        $app
            ->put(
                '/profiles/{userName:[a-zA-Z0-9]+}/tags/{tagName}',
                'App\Controller\Tags:updateOne'
            )
            ->add($permission(CompanyPermission::PUBLIC_ACTION))
            ->add($auth(Auth::CRED_TOKEN | Auth::CRED_PRIVKEY))
            ->setName('tags:updateOne');
    }

    /**
     * Delete All Tags.
     *
     * Delete all tags that belong to the requesting company.
     *
     * @apiEndpoint DELETE /companies/{companySlug}/tags
     * @apiGroup Company Tags
     * @apiAuth header key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiAuth query key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiEndpointURIFragment string companySlug veridu-ltd
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/companies/tags/deleteAll.md
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
            ->add($permission(CompanyPermission::PUBLIC_ACTION))
            ->add($auth(Auth::CRED_TOKEN | Auth::CRED_PRIVKEY))
            ->setName('tags:deleteAll');
    }

    /*
     * Retrieve a single Tag.
     *
     * Retrieves all public information from a Tag
     *
     * @apiEndpoint GET /companies/{companySlug}/tags/{userName}
     * @apiGroup Company Tags
     * @apiAuth header key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiAuth query key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiEndpointURIFragment string companySlug veridu-ltd
     * @apiEndpointURIFragment string userName
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/companies/tags/getOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Tags::getOne
     */
    private static function getOne(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/profiles/{userName:[a-zA-Z0-9]+}/tags/{tagName}',
                'App\Controller\Tags:getOne'
            )
            ->add($permission(CompanyPermission::PUBLIC_ACTION))
            ->add($auth(Auth::CRED_TOKEN | Auth::CRED_PRIVKEY))
            ->setName('tags:getOne');
    }

    /*
     * Deletes a single Tag.
     *
     * Deletes a single Tag that belongs to the requesting company.
     *
     * @apiEndpoint DELETE /companies/{companySlug}/tags/{userName}
     * @apiGroup Company Tags
     * @apiAuth header key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiAuth query key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiEndpointURIFragment string companySlug veridu-ltd
     * @apiEndpointURIFragment string userName
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/companies/tags/deleteOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Tags::deleteOne
     */
    private static function deleteOne(App $app, callable $auth, callable $permission) {
        $app
            ->delete(
                '/profiles/{userName:[a-zA-Z0-9]+}/tags/{tagName}',
                'App\Controller\Tags:deleteOne'
            )
            ->add($permission(CompanyPermission::PUBLIC_ACTION))
            ->add($auth(Auth::CRED_TOKEN | Auth::CRED_PRIVKEY))
            ->setName('tags:deleteOne');
    }
}
