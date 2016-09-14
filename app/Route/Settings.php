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
 * Settings routing definitions.
 *
 * @link docs/companies/settings/overview.md
 * @see App\Controller\Settings
 */
class Settings implements RouteInterface {
    /**
     * {@inheritdoc}
     */
    public static function getPublicNames() : array {
        return [
            'settings:listAll',
            'settings:createNew',
            'settings:getOne',
            'settings:updateOne',
            'settings:deleteOne'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function register(App $app) {
        $app->getContainer()[\App\Controller\Settings::class] = function (ContainerInterface $container) {
            return new \App\Controller\Settings(
                $container->get('repositoryFactory')->create('Setting'),
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
     * List all Settings.
     *
     * Retrieve a complete list of all settings that belong to the requesting company.
     *
     * @apiEndpoint GET /companies/{companySlug}/settings
     * @apiGroup Company Settings
     * @apiAuth header token IdentityToken XXX A valid Identity Token
     * @apiAuth query token IdentityToken XXX A valid Identity Token
     * @apiEndpointURIFragment string companySlug veridu-ltd
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/settings/listAll.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Settings::listAll
     */
    private static function listAll(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/companies/{companySlug:[a-z0-9_-]+}/settings',
                'App\Controller\Settings:listAll'
            )
            ->add($permission(
                EndpointPermission::SELF_ACTION | EndpointPermission::PARENT_ACTION, 
                Role::COMPANY_OWNER_BIT | Role::COMPANY_ADMIN_BIT
            ))
            ->add($auth(Auth::IDENTITY))
            ->setName('settings:listAll');
    }

    /**
     * Create new Setting.
     *
     * Create a new credential for the requesting identity.
     *
     * @apiEndpoint POST /companies/{companySlug}/settings
     * @apiGroup Company Settings
     * @apiAuth header token IdentityToken XXX A valid Identity Token
     * @apiAuth query token IdentityToken XXX A valid Identity Token
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/settings/createNew.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Settings::createNew
     */
    private static function createNew(App $app, callable $auth, callable $permission) {
        $app
            ->post(
                '/companies/{companySlug:[a-z0-9_-]+}/settings',
                'App\Controller\Settings:createNew'
            )
            ->add($permission(
                EndpointPermission::SELF_ACTION | EndpointPermission::PARENT_ACTION, 
                Role::COMPANY_OWNER_BIT | Role::COMPANY_ADMIN_BIT
            ))
            ->add($auth(Auth::IDENTITY))
            ->setName('settings:createNew');
    }

    /**
     * Retrieve a single Setting.
     *
     * Retrieves all public information from a Setting.
     *
     * @apiEndpoint GET /companies/{companySlug}/settings/{settingId}
     * @apiGroup Company Settings
     * @apiAuth header token IdentityToken XXX A valid Identity Token
     * @apiAuth query token IdentityToken XXX A valid Identity Token
     * @apiEndpointURIFragment string companySlug veridu-ltd
     * @apiEndpointURIFragment int settingId 1
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/settings/getOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Settings::getOne
     */
    private static function getOne(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/companies/{companySlug:[a-z0-9_-]+}/settings/{settingId:[0-9]+}',
                'App\Controller\Settings:getOne'
            )
            ->add($permission(
                EndpointPermission::SELF_ACTION | EndpointPermission::PARENT_ACTION, 
                Role::COMPANY_OWNER_BIT | Role::COMPANY_ADMIN_BIT
            ))
            ->add($auth(Auth::IDENTITY))
            ->setName('settings:getOne');
    }

    /**
     * Update a single Setting.
     *
     * Updates Setting's specific information.
     *
     * @apiEndpoint PUT /companies/{companySlug}/settings/{settingId}
     * @apiGroup Company Settings
     * @apiAuth header token IdentityToken XXX A valid Identity Token
     * @apiAuth query token IdentityToken XXX A valid Identity Token
     * @apiEndpointURIFragment string companySlug veridu-ltd
     * @apiEndpointURIFragment int settingId 1
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/settings/updateOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Settings::updateOne
     */
    private static function updateOne(App $app, callable $auth, callable $permission) {
        $app
            ->put(
                '/companies/{companySlug:[a-z0-9_-]+}/settings/{settingId:[0-9]+}',
                'App\Controller\Settings:updateOne'
            )
            ->add($permission(
                EndpointPermission::SELF_ACTION | EndpointPermission::PARENT_ACTION, 
                Role::COMPANY_OWNER_BIT | Role::COMPANY_ADMIN_BIT
            ))
            ->add($auth(Auth::IDENTITY))
            ->setName('settings:updateOne');
    }

    /**
     * Deletes a single Setting.
     *
     * Deletes a single Setting that belongs to the requesting company.
     *
     * @apiEndpoint DELETE /companies/{companySlug}/settings/{settingId}
     * @apiGroup Company Settings
     * @apiAuth header token IdentityToken XXX A valid Identity Token
     * @apiAuth query token IdentityToken XXX A valid Identity Token
     * @apiEndpointURIFragment string companySlug veridu-ltd
     * @apiEndpointURIFragment int settingId 1
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/settings/deleteOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Settings::deleteOne
     */
    private static function deleteOne(App $app, callable $auth, callable $permission) {
        $app
            ->delete(
                '/companies/{companySlug:[a-z0-9_-]+}/settings/{settingId:[0-9]+}',
                'App\Controller\Settings:deleteOne'
            )
            ->add($permission(
                EndpointPermission::SELF_ACTION | EndpointPermission::PARENT_ACTION, 
                Role::COMPANY_OWNER_BIT | Role::COMPANY_ADMIN_BIT
            ))
            ->add($auth(Auth::IDENTITY))
            ->setName('settings:deleteOne');
    }
}
