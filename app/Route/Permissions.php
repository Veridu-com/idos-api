<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Route;

use App\Entity\Role;
use App\Middleware\Auth;
use App\Middleware\EndpointPermission;
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
        $permissionMiddleware = $container->get('endpointPermissionMiddleware');

        self::listAll($app, $authMiddleware, $permissionMiddleware);
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
<<<<<<< HEAD
     * @apiAuth header token CompanyToken A valid Identity Token
     * @apiAuth query token companyToken A valid Identity Token
=======
     * @apiAuth header token IdentityToken A valid Identity Token
     * @apiAuth query token IdentityToken A valid Identity Token
>>>>>>> 38414c0f682f504064149c6715641486b5378a8f
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
                '/companies/{companySlug:[a-z0-9_-]+}/permissions',
                'App\Controller\Permissions:listAll'
            )
            ->add($permission(
                EndpointPermission::SELF_ACTION | EndpointPermission::PARENT_ACTION, 
                Role::COMPANY_OWNER_BIT | Role::COMPANY_ADMIN_BIT
            ))
            ->add($auth(Auth::IDENTITY))
            ->setName('permissions:listAll');
    }

    /**
     * Create new Permission.
     *
     * Create a new credential for the requesting company.
     *
     * @apiEndpoint POST /companies/{companySlug}/permissions
<<<<<<< HEAD
     * @apiAuth header token CompanyToken A valid Identity Token
     * @apiAuth query token companyToken A valid Identity Token
=======
     * @apiAuth header token IdentityToken A valid Identity Token
     * @apiAuth query token IdentityToken A valid Identity Token
>>>>>>> 38414c0f682f504064149c6715641486b5378a8f
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
                '/companies/{companySlug:[a-z0-9_-]+}/permissions',
                'App\Controller\Permissions:createNew'
            )
            ->add($permission(
                EndpointPermission::SELF_ACTION | EndpointPermission::PARENT_ACTION, 
                Role::COMPANY_OWNER_BIT | Role::COMPANY_ADMIN_BIT
            ))
            ->add($auth(Auth::IDENTITY))
            ->setName('permissions:createNew');
    }

    /**
<<<<<<< HEAD
     * Deletes all permissions.
     *
     * Deletes all permissions that belongs to the requesting company.
     *
     * @apiEndpoint DELETE /companies/{companySlug}/permissions
     * @apiAuth header token CompanyToken A valid Identity Token
     * @apiAuth query token companyToken A valid Identity Token
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
                '/companies/{companySlug:[a-z0-9_-]+}/permissions',
                'App\Controller\Permissions:deleteAll'
            )
            ->add($permission(EndpointPermission::SELF_ACTION | EndpointPermission::PARENT_ACTION))
            ->add($auth(Auth::COMPANY))
            ->setName('permissions:deleteAll');
    }

    /**
=======
>>>>>>> 38414c0f682f504064149c6715641486b5378a8f
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
                '/companies/{companySlug:[a-z0-9_-]+}/permissions/{routeName:[a-zA-Z]+\:[a-zA-Z]+}',
                'App\Controller\Permissions:getOne'
            )
            ->add($permission(
                EndpointPermission::SELF_ACTION | EndpointPermission::PARENT_ACTION, 
                Role::COMPANY_OWNER_BIT | Role::COMPANY_ADMIN_BIT
            ))
            ->add($auth(Auth::IDENTITY))
            ->setName('permissions:getOne');
    }

    /**
     * Deletes a single Permission.
     *
     * Deletes a single Permission that belongs to the requesting company.
     *
     * @apiEndpoint DELETE /companies/{companySlug}/permissions/{routeName}
<<<<<<< HEAD
     * @apiAuth header token CompanyToken A valid Identity Token
     * @apiAuth query token companyToken A valid Identity Token
=======
     * @apiAuth header token IdentityToken A valid Identity Token
     * @apiAuth query token IdentityToken A valid Identity Token
>>>>>>> 38414c0f682f504064149c6715641486b5378a8f
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
                '/companies/{companySlug:[a-z0-9_-]+}/permissions/{routeName:[a-zA-Z]+\:[a-zA-Z]+}',
                'App\Controller\Permissions:deleteOne'
            )
            ->add($permission(
                EndpointPermission::SELF_ACTION | EndpointPermission::PARENT_ACTION, 
                Role::COMPANY_OWNER_BIT | Role::COMPANY_ADMIN_BIT
            ))
            ->add($auth(Auth::IDENTITY))
            ->setName('permissions:deleteOne');
    }
}
