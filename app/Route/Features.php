<?php
/*/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Route;

use App\Middleware\Auth;
use App\Middleware\CompanyPermission;
use Interop\Container\ContainerInterface;
use Slim\App;

/**
 * Features routing definitions.
 *
 * @link docs/profile/features/overview.md
 * @see App\Controller\Features
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
            'features:deleteOne'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function register(App $app) {
        $app->getContainer()[\App\Controller\Features::class] = function (ContainerInterface $container) {
            return new \App\Controller\Features(
                $container->get('repositoryFactory')->create('Feature'),
                $container->get('repositoryFactory')->create('User'),
                $container->get('commandBus'),
                $container->get('commandFactory')
            );
        };

        $container            = $app->getContainer();
        $authMiddleware       = $container->get('authMiddleware');
        $permissionMiddleware = $container->get('companyPermissionMiddleware');

        self::listAll($app, $authMiddleware, $permissionMiddleware);
        self::deleteAll($app, $authMiddleware, $permissionMiddleware);
        self::createNew($app, $authMiddleware, $permissionMiddleware);
        self::getOne($app, $authMiddleware, $permissionMiddleware);
        self::updateOne($app, $authMiddleware, $permissionMiddleware);
        self::deleteOne($app, $authMiddleware, $permissionMiddleware);
    }

    /**
     * List all Features.
     *
     * Retrieve a complete list of all features that belong to the given user.
     *
     * @apiEndpoint GET /profiles/{userName}/features
     * @apiGroup Profile Features
     * @apiAuth header key credentialToken 2f476be4f457ef606f3b9177b5bf19c9 Company's credential token
     * @apiAuth query key credentialToken 2f476be4f457ef606f3b9177b5bf19c9 Company's credential token
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/profile/features/listAll.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Features::listAll
     */
    private static function listAll(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/profiles/{userName:[a-zA-Z0-9]+}/features',
                'App\Controller\Features:listAll'
            )
            ->add($permission(CompanyPermission::PRIVATE_ACTION))
            ->add($auth(Auth::CRED_TOKEN))
            ->setName('features:listAll');
    }

    /**
     * Create new Feature.
     *
     * Create a new feature for the given user.
     *
     * @apiEndpoint POST profiles/{userName}/features
     * @apiGroup Profile Features
     * @apiAuth header key credentialToken 2f476be4f457ef606f3b9177b5bf19c9 Company's credential token
     * @apiAuth query key credentialToken 2f476be4f457ef606f3b9177b5bf19c9 Company's credential token
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
    private static function createNew(App $app, callable $auth, callable $permission) {
        $app
            ->post(
                '/profiles/{userName:[a-zA-Z0-9]+}/features',
                'App\Controller\Features:createNew'
            )
            ->add($permission(CompanyPermission::PRIVATE_ACTION))
            ->add($auth(Auth::CRED_TOKEN))
            ->setName('features:createNew');
    }

    /**
     * Deletes a single Feature.
     *
     * Deletes a single Feature that belongs to the given user
     *
     * @apiEndpoint DELETE /profiles/{userName}/features/{featureSlug}
     * @apiGroup Profile Features
     * @apiAuth header key credentialToken 2f476be4f457ef606f3b9177b5bf19c9 Company's credential token
     * @apiAuth query key credentialToken 2f476be4f457ef606f3b9177b5bf19c9 Company's credential token
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/profile/features/deleteOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Features::deleteOne
     */
    private static function deleteOne(App $app, callable $auth, callable $permission) {
        $app
            ->delete(
                '/profiles/{userName:[a-zA-Z0-9_-]+}/features/{featureSlug:[a-zA-Z0-9_-]+}',
                'App\Controller\Features:deleteOne'
            )
            ->add($permission(CompanyPermission::PRIVATE_ACTION))
            ->add($auth(Auth::CRED_TOKEN))
            ->setName('features:deleteOne');
    }

    /**
     * Deletes all features.
     *
     * Deletes all features that belongs to the given user
     *
     * @apiEndpoint DELETE /profiles/{userName}/features
     * @apiGroup Profile Features
     * @apiAuth header key credentialToken 2f476be4f457ef606f3b9177b5bf19c9 Company's credential token
     * @apiAuth query key credentialToken 2f476be4f457ef606f3b9177b5bf19c9 Company's credential token
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/profile/features/deleteAll.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Features::deleteAll
     */
    private static function deleteAll(App $app, callable $auth, callable $permission) {
        $app
            ->delete(
                '/profiles/{userName:[a-zA-Z0-9]+}/features',
                'App\Controller\Features:deleteAll'
            )
            ->add($permission(CompanyPermission::PRIVATE_ACTION))
            ->add($auth(Auth::CRED_TOKEN))
            ->setName('features:deleteAll');
    }

    /**
     * Retrieve a single Feature.
     *
     * Retrieves all public information from a Feature.
     *
     * @apiEndpoint GET /profiles/{userName}/features/{featureSlug}
     * @apiGroup Profile Features
     * @apiAuth header key credentialToken 2f476be4f457ef606f3b9177b5bf19c9 Company's credential token
     * @apiAuth query key credentialToken 2f476be4f457ef606f3b9177b5bf19c9 Company's credential token
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/profile/features/getOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Features::getOne
     */
    private static function getOne(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/profiles/{userName:[a-zA-Z0-9]+}/features/{featureSlug:[a-zA-Z0-9_-]+}',
                'App\Controller\Features:getOne'
            )
            ->add($permission(CompanyPermission::PRIVATE_ACTION))
            ->add($auth(Auth::CRED_TOKEN))
            ->setName('features:getOne');
    }

    /**
     * Update a single Feature.
     *
     * Updates Feature's specific information.
     *
     * @apiEndpoint PUT /profiles/{userName}/features/{featureSlug}
     * @apiGroup Profile Features
     * @apiAuth header key credentialToken 2f476be4f457ef606f3b9177b5bf19c9 Company's credential token
     * @apiAuth query key credentialToken 2f476be4f457ef606f3b9177b5bf19c9 Company's credential token
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/profile/features/updateOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Features::updateOne
     */
    private static function updateOne(App $app, callable $auth, callable $permission) {
        $app
            ->put(
                '/profiles/{userName:[a-zA-Z0-9_-]+}/features/{featureSlug:[a-zA-Z0-9_-]+}',
                'App\Controller\Features:updateOne'
            )
            ->add($permission(CompanyPermission::PRIVATE_ACTION))
            ->add($auth(Auth::CRED_TOKEN))
            ->setName('features:updateOne');
    }
}
