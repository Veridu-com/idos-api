<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Route\User;

use App\Controller\ControllerInterface;
use App\Entity\User\RoleAccess as RoleAccessEntity;
use App\Middleware\Auth;
use App\Route\RouteInterface;
use Interop\Container\ContainerInterface;
use Slim\App;

/**
 * Role Access.
 *
 * With Access Roles you can control exactly what information (e.g. last name, phone number, etc.)
 * is available to a Company Member.
 *
 * **Note:** advanced usage only.
 *
 * @apiDisabled
 *
 * @link docs/access/roles/overview.md
 * @see \App\Controller\User\RoleAccess
 */
class RoleAccess implements RouteInterface {
    /**
     * {@inheritdoc}
     */
    public static function getPublicNames() : array {
        return [
            // 'roleAccess:listAll',
            // 'roleAccess:deleteAll',
            // 'roleAccess:createNew',
            // 'roleAccess:getOne',
            // 'roleAccess:updateOne',
            // 'roleAccess:deleteOne'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function register(App $app) : void {
        $app->getContainer()[\App\Controller\User\RoleAccess::class] = function (ContainerInterface $container) : ControllerInterface {
            return new \App\Controller\User\RoleAccess(
                $container
                    ->get('repositoryFactory')
                    ->create('User\RoleAccess'),
                $container
                    ->get('commandBus'),
                $container
                    ->get('commandFactory')
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
     * @apiGroup User
     * @apiAuth query   token UserToken  eyJ0eXAiOiJKV1QiLCJhbGciOiJIU A valid User Token
     * @apiAuth header  token userToken  eyJ0eXAiOiJKV1QiLCJhbGciOiJIU A valid User Token
     *
     * @param \Slim\App $app
     * @param callable  $auth
     * @param callable  $userPermission
     *
     * @return void
     *
     * @link docs/access/roles/listAll.md
     * @see \App\Middleware\Auth::__invoke
     * @see \App\Middleware\UserPermission::__invoke
     * @see \App\Controller\User\RoleAccess::listAll
     */
    private static function listAll(App $app, callable $auth, callable $userPermission) : void {
        $app
            ->get(
                '/access/roles',
                'App\Controller\User\RoleAccess:listAll'
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
     * @apiGroup User
     * @apiAuth query   token UserToken     eyJ0eXAiOiJKV1QiLCJhbGciOiJIU A valid User Token
     * @apiAuth header  token userToken     eyJ0eXAiOiJKV1QiLCJhbGciOiJIU A valid User Token
     *
     * @param \Slim\App $app
     * @param callable  $auth
     * @param callable  $userPermission
     *
     * @return void
     *
     * @link docs/access/roles/createNew.md
     * @see \App\Middleware\Auth::__invoke
     * @see \App\Middleware\UserPermission::__invoke
     * @see \App\Controller\User\RoleAccess::createNew
     */
    private static function createNew(App $app, callable $auth, callable $userPermission) : void {
        $app
            ->post(
                '/access/roles',
                'App\Controller\User\RoleAccess:createNew'
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
     * @apiGroup User
     * @apiAuth query   token UserToken     eyJ0eXAiOiJKV1QiLCJhbGciOiJIU A valid User Token
     * @apiAuth header  token userToken     eyJ0eXAiOiJKV1QiLCJhbGciOiJIU A valid User Token
     *
     * @param \Slim\App $app
     * @param callable  $auth
     * @param callable  $userPermission
     *
     * @return void
     *
     * @link docs/access/roles/deleteAll.md
     * @see \App\Middleware\Auth::__invoke
     * @see \App\Middleware\UserPermission::__invoke
     * @see \App\Controller\User\RoleAccess::deleteAll
     */
    private static function deleteAll(App $app, callable $auth, callable $userPermission) : void {
        $app
            ->delete(
                '/access/roles',
                'App\Controller\User\RoleAccess:deleteAll'
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
     * @apiGroup User
     * @apiAuth query   token UserToken     eyJ0eXAiOiJKV1QiLCJhbGciOiJIU A valid User Token
     * @apiAuth header  token userToken     eyJ0eXAiOiJKV1QiLCJhbGciOiJIU A valid User Token
     * @apiEndpointURIFragment int roleAccessId 5319 A valid roleAccess id
     *
     * @param \Slim\App $app
     * @param callable  $auth
     * @param callable  $userPermission
     *
     * @return void
     *
     * @link docs/access/roles/getOne.md
     * @see \App\Middleware\Auth::__invoke
     * @see \App\Middleware\UserPermission::__invoke
     * @see \App\Controller\User\RoleAccess::getOne
     */
    private static function getOne(App $app, callable $auth, callable $userPermission) : void {
        $app
            ->get(
                '/access/roles/{roleAccessId:[0-9]+}',
                'App\Controller\User\RoleAccess:getOne'
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
     * @apiGroup User
     * @apiAuth query   token UserToken     eyJ0eXAiOiJKV1QiLCJhbGciOiJIU A valid User Token
     * @apiAuth header  token userToken     eyJ0eXAiOiJKV1QiLCJhbGciOiJIU A valid User Token
     * @apiEndpointURIFragment int roleAccessId 5319 A valid roleAccess id
     *
     * @param \Slim\App $app
     * @param callable  $auth
     * @param callable  $userPermission
     *
     * @return void
     *
     * @link docs/access/roles/getOne.md
     * @see \App\Middleware\Auth::__invoke
     * @see \App\Middleware\UserPermission::__invoke
     * @see \App\Controller\User\RoleAccess::getOne
     */
    private static function updateOne(App $app, callable $auth, callable $userPermission) : void {
        $app
            ->put(
                '/access/roles/{roleAccessId:[0-9]+}',
                'App\Controller\User\RoleAccess:updateOne'
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
     * @apiGroup User
     * @apiAuth query   token UserToken     eyJ0eXAiOiJKV1QiLCJhbGciOiJIU A valid User Token
     * @apiAuth header  token userToken     eyJ0eXAiOiJKV1QiLCJhbGciOiJIU A valid User Token
     * @apiEndpointURIFragment int roleAccessId 5319 A valid roleAccess id
     *
     * @param \Slim\App $app
     * @param callable  $auth
     * @param callable  $userPermission
     *
     * @return void
     *
     * @link docs/access/roles/deleteOne.md
     * @see \App\Middleware\Auth::__invoke
     * @see \App\Middleware\UserPermission::__invoke
     * @see \App\Controller\User\RoleAccess::deleteOne
     */
    private static function deleteOne(App $app, callable $auth, callable $userPermission) : void {
        $app
            ->delete(
                '/access/roles/{roleAccessId:[0-9]+}',
                'App\Controller\User\RoleAccess:deleteOne'
            )
            ->add($userPermission('roleAccess:createNew', RoleAccessEntity::ACCESS_EXECUTE))
            ->add($auth(Auth::USER))
            ->setName('roleAccess:deleteOne');
    }
}
