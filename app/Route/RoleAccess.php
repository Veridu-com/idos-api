<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Route;

use App\Entity\RoleAccess as RoleAccessEntity;
use App\Middleware\Auth;
use Interop\Container\ContainerInterface;
use Slim\App;

/**
 * RoleAccess routing definitions.
 *
 * @link docs/access/roles/{companySlug}/{userName}/overview.md
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
            'roleAccess:listAllFromRole',
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
        $userPermissionMiddleware   = $container->get('userPermissionMiddleware');

        self::listAll($app, $authMiddleware, $userPermissionMiddleware);
        self::listAllFromRole($app, $authMiddleware, $userPermissionMiddleware);
        self::getOne($app, $authMiddleware, $userPermissionMiddleware);
        self::createNew($app, $authMiddleware, $userPermissionMiddleware);
        self::updateOne($app, $authMiddleware, $userPermissionMiddleware);
        self::deleteOne($app, $authMiddleware, $userPermissionMiddleware);
        self::deleteAll($app, $authMiddleware, $userPermissionMiddleware);
    }

    /**
     * List all RoleAccess from a target User.
     *
     * Retrieve a complete list of all Role Access that belong to the target User.
     *
     * @apiEndpoint GET /access/roles/{companySlug}/{userName}
     * 
     * @apiAuth header  key compPrivKey     Company's Private Key
     * @apiAuth query   key compPrivKey     Company's Private Key
     * @apiAuth query   key userPrivKey     User's Private Key
     * @apiAuth header  key userPrivKey     User's Private Key
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/access/roles/roleAccess/listAll.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\UserPermission::__invoke
     * @see App\Controller\RoleAccess::listAll
     */
    private static function listAll(App $app, callable $auth, callable $userPermission) {
        $app
            ->get(
                '/access/roles',
                'App\Controller\RoleAccess:listAll'
            )
            ->add($userPermission('roleAccess:listAll', RoleAccessEntity::ACCESS_READ))
            ->add($auth(Auth::USER_PRIVKEY | Auth::COMP_PRIVKEY))
            ->setName('roleAccess:listAll');
    }

    /**
     * Create new RoleAccess.
     *
     * Create a new Role Access for the target User.
     *
     * @apiEndpoint POST /access/roles/{companySlug}/{userName}
     * 
     * @apiAuth header  key compPrivKey     Company's Private Key
     * @apiAuth query   key compPrivKey     Company's Private Key
     * @apiAuth query   key userPrivKey     User's Private Key
     * @apiAuth header  key userPrivKey     User's Private Key
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/access/roles/roleAccess/createNew.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\UserPermission::__invoke
     * @see App\Controller\RoleAccess::createNew
     */
    private static function createNew(App $app, callable $auth, callable $userPermission) {
        $app
            ->post(
                '/access/roles',
                'App\Controller\RoleAccess:createNew'
            )
            ->add($userPermission('roleAccess:createNew', RoleAccessEntity::ACCESS_WRITE))
            ->add($auth(Auth::USER_PRIVKEY | Auth::COMP_PRIVKEY))
            ->setName('roleAccess:createNew');
    }

    /**
     * Deletes all roleAccess.
     *
     * Deletes all roleAccess that belongs to the target User.
     *
     * @apiEndpoint DELETE /access/roles/{companySlug}/{userName}
     * 
     * @apiAuth header  key compPrivKey     Company's Private Key
     * @apiAuth query   key compPrivKey     Company's Private Key
     * @apiAuth query   key userPrivKey     User's Private Key
     * @apiAuth header  key userPrivKey     User's Private Key
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/access/roles/roleAccess/deleteAll.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\UserPermission::__invoke
     * @see App\Controller\RoleAccess::deleteAll
     */
    private static function deleteAll(App $app, callable $auth, callable $userPermission) {
        $app
            ->delete(
                '/access/roles',
                'App\Controller\RoleAccess:deleteAll'
            )
            ->add($userPermission('roleAccess:deleteAll', RoleAccessEntity::ACCESS_WRITE))
            ->add($auth(Auth::USER_PRIVKEY | Auth::COMP_PRIVKEY))
            ->setName('roleAccess:deleteAll');
    }

    /**
     * Retrieve a single RoleAccess.
     *
     * Retrieves all public information from a RoleAccess.
     *
     * @apiEndpoint GET /access/roles/{companySlug}/{userName}/{routeName}
     * 
     * @apiAuth header  key compPrivKey     Company's Private Key
     * @apiAuth query   key compPrivKey     Company's Private Key
     * @apiAuth query   key userPrivKey     User's Private Key
     * @apiAuth header  key userPrivKey     User's Private Key
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/access/roles/roleAccess/listAllFromRole.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\UserPermission::__invoke
     * @see App\Controller\RoleAccess::listAllFromRole
     */
    private static function listAllFromRole(App $app, callable $auth, callable $userPermission) {
        $app
            ->get(
                '/access/roles/{companySlug:[a-zA-Z0-9_-]+}/{userName:[a-zA-Z0-9_-]+}/{roleName:[a-zA-Z0-9_-]+}',
                'App\Controller\RoleAccess:listAllFromRole'
            )
            ->add($userPermission('roleAccess:listAllFromRole', RoleAccessEntity::ACCESS_READ))
            ->add($auth(Auth::USER_PRIVKEY | Auth::COMP_PRIVKEY))
            ->setName('roleAccess:listAllFromRole');
    }

    /**
     * Retrieve a single RoleAccess.
     *
     * Retrieves all public information from a RoleAccess.
     *
     * @apiEndpoint GET /access/roles/{companySlug}/{userName}
     * 
     * @apiAuth header  key compPrivKey     Company's Private Key
     * @apiAuth query   key compPrivKey     Company's Private Key
     * @apiAuth query   key userPrivKey     User's Private Key
     * @apiAuth header  key userPrivKey     User's Private Key
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/access/roles/roleAccess/getOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\UserPermission::__invoke
     * @see App\Controller\RoleAccess::getOne
     */
    private static function getOne(App $app, callable $auth, callable $userPermission) {
        $app
            ->get(
                '/access/roles/{companySlug:[a-zA-Z0-9_-]+}/{userName:[a-zA-Z0-9_-]+}/{roleName:[a-zA-Z0-9_-]+}/{resource}',
                'App\Controller\RoleAccess:getOne'
            )
            ->add($userPermission('roleAccess:getOne', RoleAccessEntity::ACCESS_READ))
            ->add($auth(Auth::USER_PRIVKEY | Auth::COMP_PRIVKEY))
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
     * @link docs/access/roles/roleAccess/getOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\UserPermission::__invoke
     * @see App\Controller\RoleAccess::getOne
     */
    private static function updateOne(App $app, callable $auth, callable $userPermission) {
        $app
            ->put(
                '/access/roles/{companySlug:[a-zA-Z0-9_-]+}/{userName:[a-zA-Z0-9_-]+}/{roleName:[a-zA-Z0-9_-]+}/{resource}',
                'App\Controller\RoleAccess:updateOne'
            )
            ->add($userPermission('roleAccess:updateOne', RoleAccessEntity::ACCESS_WRITE))
            ->add($auth(Auth::USER_PRIVKEY | Auth::COMP_PRIVKEY))
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
     * @link docs/access/roles/roleAccess/deleteOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\UserPermission::__invoke
     * @see App\Controller\RoleAccess::deleteOne
     */
    private static function deleteOne(App $app, callable $auth, callable $userPermission) {
        $app
            ->delete(
                '/access/roles/{companySlug:[a-zA-Z0-9_-]+}/{userName:[a-zA-Z0-9_-]+}/{roleName:[a-zA-Z0-9_-]+}/{resource}',
                'App\Controller\RoleAccess:deleteOne'
            )
            ->add($userPermission('roleAccess:deleteOne', RoleAccessEntity::ACCESS_WRITE))
            ->add($auth(Auth::USER_PRIVKEY | Auth::COMP_PRIVKEY))
            ->setName('roleAccess:deleteOne');
    }
}
