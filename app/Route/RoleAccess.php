<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Route;

use App\Entity\Role;
use App\Entity\RoleAccess as RoleAccessEntity;
use App\Middleware\Auth;
use Interop\Container\ContainerInterface;
use Slim\App;

/**
 * Role Access.
 *
 * RoleAccess is the level of permission about a User that is accessible from specific sources. It is used to limit what sensitive information (eg. last name, phone number etc) is available to specific Members or Users.
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

        $container      = $app->getContainer();
        $authMiddleware = $container->get('authMiddleware');
        $userPermission = $container->get('userPermissionMiddleware');

        self::listAll($app, $authMiddleware, $userPermission);
        self::getOne($app, $authMiddleware, $userPermission);
        self::createNew($app, $authMiddleware, $userPermission);
        self::updateOne($app, $authMiddleware, $userPermission);
        self::deleteOne($app, $authMiddleware, $userPermission);
        self::deleteAll($app, $authMiddleware, $userPermission);
    }

    /**
     * List all RoleAccess from the acting User.
     *
     * Retrieve a complete list of all Role Access that belong to the acting User.
     *
     * @apiEndpoint GET /access/roles
     * @apiGroup Access Roles
     * @apiAuth query   token UserToken  eyJ0eXAiOiJKV1QiLCJhbGciOiJIU A valid User Token
     * @apiAuth header  token userToken  eyJ0eXAiOiJKV1QiLCJhbGciOiJIU A valid User Token
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $userPermission
     *
     * @return void
     *
     * @link docs/access/roles/listAll.md
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
            ->add($auth(Auth::USER))
            ->setName('roleAccess:listAll');
    }

    /**
     * Create new RoleAccess.
     *
     * Creates a new Role Access for the acting User.
     *
     * @apiEndpoint POST /access/roles
     * @apiGroup Access Roles
     * @apiAuth query   token UserToken     eyJ0eXAiOiJKV1QiLCJhbGciOiJIU A valid User Token
     * @apiAuth header  token userToken     eyJ0eXAiOiJKV1QiLCJhbGciOiJIU A valid User Token
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $userPermission
     *
     * @return void
     *
     * @link docs/access/roles/createNew.md
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
            ->add($userPermission('roleAccess:createNew', RoleAccessEntity::ACCESS_READ | RoleAccessEntity::ACCESS_WRITE))
            ->add($auth(Auth::USER))
            ->setName('roleAccess:createNew');
    }

    /**
     * Deletes all roleAccess.
     *
     * Deletes all RoleAccesses that belong to the acting User.
     *
     * @apiEndpoint DELETE /access/roles
     * @apiGroup Access Roles
     * @apiAuth query   token UserToken     eyJ0eXAiOiJKV1QiLCJhbGciOiJIU A valid User Token
     * @apiAuth header  token userToken     eyJ0eXAiOiJKV1QiLCJhbGciOiJIU A valid User Token
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $userPermission
     *
     * @return void
     *
     * @link docs/access/roles/deleteAll.md
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
            ->add($userPermission('roleAccess:createNew', RoleAccessEntity::ACCESS_EXECUTE))
            ->add($auth(Auth::USER))
            ->setName('roleAccess:deleteAll');
    }

    /**
     * Retrieve a single RoleAccess.
     *
     * Retrieves all public information from a RoleAccess.
     *
     * @apiEndpoint GET /access/roles/{roleAccessId}
     * @apiGroup Access Roles
     * @apiAuth query   token UserToken     eyJ0eXAiOiJKV1QiLCJhbGciOiJIU A valid User Token
     * @apiAuth header  token userToken     eyJ0eXAiOiJKV1QiLCJhbGciOiJIU A valid User Token
     * @apiEndpointURIFragment int roleAccessId 5319 A valid roleAccess id
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $userPermission
     *
     * @return void
     *
     * @link docs/access/roles/getOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\UserPermission::__invoke
     * @see App\Controller\RoleAccess::getOne
     */
    private static function getOne(App $app, callable $auth, callable $userPermission) {
        $app
            ->get(
                '/access/roles/{roleAccessId:[0-9]+}',
                'App\Controller\RoleAccess:getOne'
            )
            ->add($userPermission('roleAccess:createNew', RoleAccessEntity::ACCESS_READ))
            ->add($auth(Auth::USER))
            ->setName('roleAccess:getOne');
    }

    /**
     * Retrieve a single RoleAccess.
     *
     * Retrieves all public information from a RoleAccess.
     *
     * @apiEndpoint GET /access/roles/{roleAccessId}
     * @apiGroup Access Roles
     * @apiAuth query   token UserToken     eyJ0eXAiOiJKV1QiLCJhbGciOiJIU A valid User Token
     * @apiAuth header  token userToken     eyJ0eXAiOiJKV1QiLCJhbGciOiJIU A valid User Token
     * @apiEndpointURIFragment int roleAccessId 5319 A valid roleAccess id
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $userPermission
     *
     * @return void
     *
     * @link docs/access/roles/getOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\UserPermission::__invoke
     * @see App\Controller\RoleAccess::getOne
     */
    private static function updateOne(App $app, callable $auth, callable $userPermission) {
        $app
            ->put(
                '/access/roles/{roleAccessId:[0-9]+}',
                'App\Controller\RoleAccess:updateOne'
            )
            ->add($userPermission('roleAccess:createNew', RoleAccessEntity::ACCESS_READ | RoleAccessEntity::ACCESS_WRITE))
            ->add($auth(Auth::USER))
            ->setName('roleAccess:updateOne');
    }

    /**
     * Deletes a single RoleAccess.
     *
     * Deletes a single RoleAccess that belongs to the requesting user.
     *
     * @apiEndpoint DELETE /access/roles/{roleAccessId}
     * @apiGroup Access Roles
     * @apiAuth query   token UserToken     eyJ0eXAiOiJKV1QiLCJhbGciOiJIU A valid User Token
     * @apiAuth header  token userToken     eyJ0eXAiOiJKV1QiLCJhbGciOiJIU A valid User Token
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $userPermission
     *
     * @return void
     *
     * @link docs/access/roles/deleteOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\UserPermission::__invoke
     * @see App\Controller\RoleAccess::deleteOne
     */
    private static function deleteOne(App $app, callable $auth, callable $userPermission) {
        $app
            ->delete(
                '/access/roles/{roleAccessId:[0-9]+}',
                'App\Controller\RoleAccess:deleteOne'
            )
            ->add($userPermission('roleAccess:createNew', RoleAccessEntity::ACCESS_EXECUTE))
            ->add($auth(Auth::USER))
            ->setName('roleAccess:deleteOne');
    }
}
