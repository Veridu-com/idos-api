<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Route;

use App\Middleware\Auth;
use Interop\Container\ContainerInterface;
use Slim\App;

/**
 * RoleAccess routing definitions.
 *
 * @link docs/access/roles/overview.md
 * @see App\Controller\RoleAccess
 */
class RoleAccess implements RouteInterface {
    /**
     * {@inheritdoc}
     */
    public static function getPublicNames() : array {
        return [
            'roleAccess:listAll',
            'roleAccess:deleteAll',
            'roleAccess:createNew',
            'roleAccess:getOne',
            'roleAccess:updateOne',
            'roleAccess:deleteOne'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function register(App $app) {
        $app->getContainer()[\App\Controller\RoleAccess::class] = function (ContainerInterface $container) {
            return new \App\Controller\RoleAccess(
                $container->get('repositoryFactory')->create('RoleAccess'),
                $container->get('commandBus'),
                $container->get('commandFactory'),
                $container->get('optimus')
            );
        };

        $container                  = $app->getContainer();
        $authMiddleware             = $container->get('authMiddleware');

        self::listAll($app, $authMiddleware);
        self::getOne($app, $authMiddleware);
        self::createNew($app, $authMiddleware);
        self::updateOne($app, $authMiddleware);
        self::deleteOne($app, $authMiddleware);
        self::deleteAll($app, $authMiddleware);
    }

    /**
     * List all RoleAccess from the acting User.
     *
     * Retrieve a complete list of all Role Access that belong to the acting User.
     *
     * @apiEndpoint GET /access/roles/{companySlug}/{userName}
     * 
     * @apiAuth query   key userPrivKey     User's Private Key
     * @apiAuth header  key userPrivKey     User's Private Key
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/access/roles/listAll.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\UserPermission::__invoke
     * @see App\Controller\RoleAccess::listAll
     */
    private static function listAll(App $app, callable $auth) {
        $app
            ->get(
                '/access/roles',
                'App\Controller\RoleAccess:listAll'
            )
            ->add($auth(Auth::USER_PRIVKEY))
            ->setName('roleAccess:listAll');
    }

    /**
     * Create new RoleAccess.
     *
     * Create a new Role Access for the acting User.
     *
     * @apiEndpoint POST /access/roles
     * 
     * @apiAuth query   key userPrivKey     User's Private Key
     * @apiAuth header  key userPrivKey     User's Private Key
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/access/roles/createNew.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\UserPermission::__invoke
     * @see App\Controller\RoleAccess::createNew
     */
    private static function createNew(App $app, callable $auth) {
        $app
            ->post(
                '/access/roles',
                'App\Controller\RoleAccess:createNew'
            )
            ->add($auth(Auth::USER_PRIVKEY))
            ->setName('roleAccess:createNew');
    }

    /**
     * Deletes all roleAccess.
     *
     * Deletes all roleAccess that belongs to the acting User.
     *
     * @apiEndpoint DELETE /access/roles
     * 
     * @apiAuth query   key userPrivKey     User's Private Key
     * @apiAuth header  key userPrivKey     User's Private Key
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/access/roles/deleteAll.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\UserPermission::__invoke
     * @see App\Controller\RoleAccess::deleteAll
     */
    private static function deleteAll(App $app, callable $auth) {
        $app
            ->delete(
                '/access/roles',
                'App\Controller\RoleAccess:deleteAll'
            )
            ->add($auth(Auth::USER_PRIVKEY))
            ->setName('roleAccess:deleteAll');
    }

    /**
     * Retrieve a single RoleAccess.
     *
     * Retrieves all public information from a RoleAccess.
     *
     * @apiEndpoint GET /access/roles/{roleName}/{resource}
     * 
     * @apiAuth query   key userPrivKey     User's Private Key
     * @apiAuth header  key userPrivKey     User's Private Key
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/access/roles/getOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\UserPermission::__invoke
     * @see App\Controller\RoleAccess::getOne
     */
    private static function getOne(App $app, callable $auth) {
        $app
            ->get(
                '/access/roles/{roleName:[a-zA-Z0-9_-]+}/{resource:[a-zA-Z0-9_-]+\:[a-zA-Z0-9_-]+}',
                'App\Controller\RoleAccess:getOne'
            )
            ->add($auth(Auth::USER_PRIVKEY))
            ->setName('roleAccess:getOne');
    }

    /**
     * Retrieve a single RoleAccess.
     *
     * Retrieves all public information from a RoleAccess.
     *
     * @apiEndpoint GET /access/roles/{companySlug}/{userName}/{routeName}
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/access/roles/getOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\UserPermission::__invoke
     * @see App\Controller\RoleAccess::getOne
     */
    private static function updateOne(App $app, callable $auth) {
        $app
            ->put(
                '/access/roles/{roleName:[a-zA-Z0-9_-]+}/{resource:[a-zA-Z0-9_-]+\:[a-zA-Z0-9_-]+}',
                'App\Controller\RoleAccess:updateOne'
            )
            ->add($auth(Auth::USER_PRIVKEY))
            ->setName('roleAccess:updateOne');
    }

    /**
     * Deletes a single RoleAccess.
     *
     * Deletes a single RoleAccess that belongs to the requesting user.
     *
     * @apiEndpoint DELETE /access/roles/{companySlug}/{userName}/{routeName}
     * @apiAuth header key compPrivKey User's Private Key
     * @apiAuth query key compPrivKey User's Private Key
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/access/roles/deleteOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\UserPermission::__invoke
     * @see App\Controller\RoleAccess::deleteOne
     */
    private static function deleteOne(App $app, callable $auth) {
        $app
            ->delete(
                '/access/roles/{roleName:[a-zA-Z0-9_-]+}/{resource:[a-zA-Z0-9_-]+\:[a-zA-Z0-9_-]+}',
                'App\Controller\RoleAccess:deleteOne'
            )
            ->add($auth(Auth::USER_PRIVKEY))
            ->setName('roleAccess:deleteOne');
    }
}
