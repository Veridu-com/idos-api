<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Route;

use Slim\App;
use App\Middleware\Auth;
use App\Middleware\UserPermission;
use Interop\Container\ContainerInterface;
use App\Entity\RoleAccess as RoleAccessEntity;

/**
 * RoleAccess routing definitions.
 *
 * @link docs/access/roles/{username}/overview.md
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
     * List all RoleAccess.
     *
     * Retrieve a complete list of all roleAccess that belong to the requesting company.
     *
     * @apiEndpoint GET /companies/{companySlug}/roleAccess
     * @apiAuth header key compPrivKey User's Private Key
     * @apiAuth query key compPrivKey User's Private Key
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/companies/roleAccess/listAll.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\UserPermission::__invoke
     * @see App\Controller\RoleAccess::listAll
     */
    private static function listAll(App $app, callable $auth, callable $userPermission) {
        $app
            ->get(
                '/access/roles/{companySlug}/{userName:[a-zA-Z0-9_-]+}',
                'App\Controller\RoleAccess:listAll'
            )
            ->add($userPermission('roleAccess:listAll', RoleAccessEntity::ACCESS_READ))
            ->add($auth(Auth::USER_PRIVKEY | Auth::COMP_PRIVKEY))
            ->setName('roleAccess:listAll');
    }

    /**
     * Create new RoleAccess.
     *
     * Create a new credential for the requesting company.
     *
     * @apiEndpoint POST /companies/{companySlug}/roleAccess
     * @apiAuth header key compPrivKey User's Private Key
     * @apiAuth query key compPrivKey User's Private Key
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/companies/roleAccess/createNew.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\UserPermission::__invoke
     * @see App\Controller\RoleAccess::createNew
     */
    private static function createNew(App $app, callable $auth, callable $userPermission) {
        $app
            ->post(
                '/access/roles/{companySlug}/{userName:[a-zA-Z0-9_-]+}',
                'App\Controller\RoleAccess:createNew'
            )
            ->add($userPermission('roleAccess:createNew', RoleAccessEntity::ACCESS_WRITE))
            ->add($auth(Auth::USER_PRIVKEY | Auth::COMP_PRIVKEY))
            ->setName('roleAccess:createNew');
    }

    /**
     * Deletes all roleAccess.
     *
     * Deletes all roleAccess that belongs to the requesting company.
     *
     * @apiEndpoint DELETE /companies/{companySlug}/roleAccess
     * @apiAuth header key compPrivKey User's Private Key
     * @apiAuth query key compPrivKey User's Private Key
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/companies/roleAccess/deleteAll.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\UserPermission::__invoke
     * @see App\Controller\RoleAccess::deleteAll
     */
    private static function deleteAll(App $app, callable $auth, callable $userPermission) {
        $app
            ->delete(
                '/access/roles/{companySlug}/{userName:[a-zA-Z0-9_-]+}',
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
     * @apiEndpoint GET /companies/{companySlug}/roleAccess/{routeName}
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/companies/roleAccess/listAllFromRole.md
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
     * @apiEndpoint GET /companies/{companySlug}/roleAccess/{routeName}
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/companies/roleAccess/getOne.md
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
     * @apiEndpoint GET /companies/{companySlug}/roleAccess/{routeName}
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/companies/roleAccess/getOne.md
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
     * @apiEndpoint DELETE /companies/{companySlug}/roleAccess/{routeName}
     * @apiAuth header key compPrivKey User's Private Key
     * @apiAuth query key compPrivKey User's Private Key
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/companies/roleAccess/deleteOne.md
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
