<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Route;

use App\Controller\ControllerInterface;
use App\Entity\Role;
use App\Middleware\Auth;
use App\Middleware\EndpointPermission;
use Interop\Container\ContainerInterface;
use Slim\App;

/**
 * Companies routing definitions.
 *
 * @link docs/companies/overview.md
 * @see App\Controller\Companies
 */
class Companies implements RouteInterface {
    /**
     * {@inheritdoc}
     */
    public static function getPublicNames() : array {
        return [
            'companies:listAll',
            'companies:createNew',
            'companies:getOne',
            'companies:updateOne',
            'companies:deleteOne',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function register(App $app) {
        $app->getContainer()[\App\Controller\Companies::class] = function (ContainerInterface $container) : ControllerInterface {
            return new \App\Controller\Companies(
                $container->get('repositoryFactory')->create('Company'),
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
        self::updateOne($app, $authMiddleware, $permissionMiddleware);
        self::deleteOne($app, $authMiddleware, $permissionMiddleware);
    }

    /**
     * List all Companies.
     *
     * Retrieve a complete list of companies that the Identity is a Member.
     *
     * @apiEndpoint GET /companies
     * @apiGroup Company
     * @apiAuth header token IdentityToken XXX A valid Identity Token
     * @apiAuth query token IdentityToken XXX A valid Identity Token
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/companies/listAll.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Companies::listAll
     */
    private static function listAll(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/companies',
                'App\Controller\Companies:listAll'
            )
            ->add($permission(
                EndpointPermission::SELF_ACTION | EndpointPermission::PARENT_ACTION, 
                Role::COMPANY_OWNER_BIT | Role::COMPANY_ADMIN_BIT
            ))
            ->add($auth(Auth::IDENTITY))
            ->setName('companies:listAll');
    }

    /**
     * Create new Company.
     *
     * Create a new child company for the company.
     *
     * @apiEndpoint POST /companies/{companySlug}
     * @apiGroup Company
     * @apiEndpointURIFragment string companySlug veridu-ltd
     * 
     * @apiAuth header token IdentityToken XXX A valid Identity Token
     * @apiAuth query token IdentityToken XXX A valid Identity Token
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/companies/createNew.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Companies::createNew
     */
    private static function createNew(App $app, callable $auth, callable $permission) {
        $app
            ->post(
                '/companies/{companySlug:[a-z0-9_-]+}',
                'App\Controller\Companies:createNew'
            )
            ->add($permission(
                EndpointPermission::SELF_ACTION | EndpointPermission::PARENT_ACTION, 
                Role::COMPANY_OWNER_BIT | Role::COMPANY_ADMIN_BIT
            ))
            ->add($auth(Auth::IDENTITY))
            ->setName('companies:createNew');
    }

    /**
     * Retrieve a single Company.
     *
     * Retrieves all public information from a Company.
     *
     * @apiEndpoint GET /companies/{companySlug}
     * @apiGroup Company
     * @apiEndpointURIFragment string companySlug veridu-ltd
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/companies/getOne.md
     * @see App\Controller\Companies::getOne
     */
    private static function getOne(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/companies/{companySlug:[a-z0-9_-]+}',
                'App\Controller\Companies:getOne'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::NONE))
            ->setName('companies:getOne');
    }

    /**
     * Update a single Company.
     *
     * Updates Company's specific information.
     *
     * @apiEndpoint PUT /companies/{companySlug}
     * @apiGroup Company
     * @apiAuth header token IdentityToken XXX A valid Identity Token
     * @apiAuth query token IdentityToken XXX A valid Identity Token
     * @apiEndpointURIFragment string companySlug veridu-ltd
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/companies/updateOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Companies::updateOne
     */
    private static function updateOne(App $app, callable $auth, callable $permission) {
        $app
            ->put(
                '/companies/{companySlug:[a-z0-9_-]+}',
                'App\Controller\Companies:updateOne'
            )
            ->add($permission(
                EndpointPermission::SELF_ACTION | EndpointPermission::PARENT_ACTION, 
                Role::COMPANY_OWNER_BIT | Role::COMPANY_ADMIN_BIT
            ))
            ->add($auth(Auth::IDENTITY))
            ->setName('companies:updateOne');
    }

    /**
     * Deletes a single Company.
     *
     * Deletes the requesting company or a child company that belongs to it.
     *
     * @apiEndpoint DELETE /companies/{companySlug}
     * @apiGroup Company
     * @apiAuth header token IdentityToken XXX A valid Identity Token
     * @apiAuth query token IdentityToken XXX A valid Identity Token
     * @apiEndpointURIFragment string companySlug veridu-ltd
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/companies/deleteOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Companies::deleteOne
     */
    private static function deleteOne(App $app, callable $auth, callable $permission) {
        $app
            ->delete(
                '/companies/{companySlug:[a-z0-9_-]+}',
                'App\Controller\Companies:deleteOne'
            )
            ->add($permission(
                EndpointPermission::SELF_ACTION | EndpointPermission::PARENT_ACTION, 
                Role::COMPANY_OWNER_BIT | Role::COMPANY_ADMIN_BIT
            ))
            ->add($auth(Auth::IDENTITY))
            ->setName('companies:deleteOne');
    }
}
