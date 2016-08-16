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
 * Permissions routing definitions.
 *
 * @link docs/companies/permissions/overview.md
 * @see App\Controller\Permissions
 */
class Permissions implements RouteInterface {
    /**
     * {@inheritdoc}
     */
    public static function getPublicNames() : array {
        return [
            'permissions:listAll',
            'permissions:deleteAll',
            'permissions:createNew',
            'permissions:getOne',
            'permissions:deleteOne'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function register(App $app) {
        $app->getContainer()[\App\Controller\Permissions::class] = function (ContainerInterface $container) {
            return new \App\Controller\Permissions(
                $container->get('repositoryFactory')->create('Permission'),
                $container->get('commandBus'),
                $container->get('commandFactory'),
                $container->get('optimus')
            );
        };

        $container            = $app->getContainer();
        $authMiddleware       = $container->get('authMiddleware');
        $permissionMiddleware = $container->get('companyPermissionMiddleware');

        self::listAll($app, $authMiddleware, $permissionMiddleware);
        self::deleteAll($app, $authMiddleware, $permissionMiddleware);
        self::createNew($app, $authMiddleware, $permissionMiddleware);
        self::getOne($app, $authMiddleware, $permissionMiddleware);
        self::deleteOne($app, $authMiddleware, $permissionMiddleware);
    }

    /**
     * List all Permissions.
     *
     * Retrieve a complete list of all permissions that belong to the requesting company.
     *
     * @apiEndpoint GET /companies/{companySlug}/permissions
     * @apiAuth header key compPrivKey Company's Private Key
     * @apiAuth query key compPrivKey Company's Private Key
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/companies/permissions/listAll.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Permissions::listAll
     */
    private static function listAll(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/companies/{companySlug:[a-zA-Z0-9_-]+}/permissions',
                'App\Controller\Permissions:listAll'
            )
            ->add($permission(CompanyPermission::PRIVATE_ACTION))
            ->add($auth(Auth::COMP_PRIVKEY))
            ->setName('permissions:listAll');
    }

    /**
     * Create new Permission.
     *
     * Create a new credential for the requesting company.
     *
     * @apiEndpoint POST /companies/{companySlug}/permissions
     * @apiAuth header key compPrivKey Company's Private Key
     * @apiAuth query key compPrivKey Company's Private Key
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/companies/permissions/createNew.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Permissions::createNew
     */
    private static function createNew(App $app, callable $auth, callable $permission) {
        $app
            ->post(
                '/companies/{companySlug:[a-zA-Z0-9_-]+}/permissions',
                'App\Controller\Permissions:createNew'
            )
            ->add($permission(CompanyPermission::PRIVATE_ACTION))
            ->add($auth(Auth::COMP_PRIVKEY))
            ->setName('permissions:createNew');
    }

    /**
     * Deletes all permissions.
     *
     * Deletes all permissions that belongs to the requesting company.
     *
     * @apiEndpoint DELETE /companies/{companySlug}/permissions
     * @apiAuth header key compPrivKey Company's Private Key
     * @apiAuth query key compPrivKey Company's Private Key
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/companies/permissions/deleteAll.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Permissions::deleteAll
     */
    private static function deleteAll(App $app, callable $auth, callable $permission) {
        $app
            ->delete(
                '/companies/{companySlug:[a-zA-Z0-9_-]+}/permissions',
                'App\Controller\Permissions:deleteAll'
            )
            ->add($permission(CompanyPermission::PRIVATE_ACTION))
            ->add($auth(Auth::COMP_PRIVKEY))
            ->setName('permissions:deleteAll');
    }

    /**
     * Retrieve a single Permission.
     *
     * Retrieves all public information from a Permission.
     *
     * @apiEndpoint GET /companies/{companySlug}/permissions/{routeName}
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/companies/permissions/getOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Permissions::getOne
     */
    private static function getOne(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                // TODO: Regex that matches route:Names
                '/companies/{companySlug:[a-zA-Z0-9_-]+}/permissions/{routeName}',
                'App\Controller\Permissions:getOne'
            )
            ->add($permission(CompanyPermission::PRIVATE_ACTION))
            ->add($auth(Auth::COMP_PRIVKEY))
            ->setName('permissions:getOne');
    }

    /**
     * Deletes a single Permission.
     *
     * Deletes a single Permission that belongs to the requesting company.
     *
     * @apiEndpoint DELETE /companies/{companySlug}/permissions/{routeName}
     * @apiAuth header key compPrivKey Company's Private Key
     * @apiAuth query key compPrivKey Company's Private Key
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/companies/permissions/deleteOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Permissions::deleteOne
     */
    private static function deleteOne(App $app, callable $auth, callable $permission) {
        $app
            ->delete(
                '/companies/{companySlug:[a-zA-Z0-9_-]+}/permissions/{routeName}',
                'App\Controller\Permissions:deleteOne'
            )
            ->add($permission(CompanyPermission::PRIVATE_ACTION))
            ->add($auth(Auth::COMP_PRIVKEY))
            ->setName('permissions:deleteOne');
    }
}