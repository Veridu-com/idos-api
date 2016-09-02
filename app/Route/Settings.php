<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Route;

use App\Middleware\Auth;
use App\Middleware\EndpointPermission;
use Interop\Container\ContainerInterface;
use Slim\App;

/**
 * Settings routing definitions.
 *
 * @link docs/management/settings/overview.md
 * @see App\Controller\Settings
 */
class Settings implements RouteInterface {
    /**
     * {@inheritdoc}
     */
    public static function getPublicNames() : array {
        return [
            'settings:listAll',
            'settings:deleteAll',
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
        self::deleteAll($app, $authMiddleware, $permissionMiddleware);
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
     * @apiEndpoint GET /management/settings
     * @apiGroup Company Settings
     * @apiAuth header token CompanyToken XXX A valid Company Token
     * @apiAuth query token CompanyToken XXX A valid Company Token
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/management/settings/listAll.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Settings::listAll
     */
    private static function listAll(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/management/settings',
                'App\Controller\Settings:listAll'
            )
            ->add($permission(EndpointPermission::PRIVATE_ACTION))
            ->add($auth(Auth::COMPANY))
            ->setName('settings:listAll');
    }

    /**
     * Create new Setting.
     *
     * Create a new credential for the requesting company.
     *
     * @apiEndpoint POST /management/settings
     * @apiGroup Company Settings
     * @apiAuth header token CompanyToken XXX A valid Company Token
     * @apiAuth query token CompanyToken XXX A valid Company Token
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/management/settings/createNew.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Settings::createNew
     */
    private static function createNew(App $app, callable $auth, callable $permission) {
        $app
            ->post(
                '/management/settings',
                'App\Controller\Settings:createNew'
            )
            ->add($permission(EndpointPermission::PRIVATE_ACTION))
            ->add($auth(Auth::COMPANY))
            ->setName('settings:createNew');
    }

    /**
     * Deletes all settings.
     *
     * Deletes all settings that belongs to the requesting company.
     *
     * @apiEndpoint DELETE /management/settings
     * @apiGroup Company Settings
     * @apiAuth header token CompanyToken XXX A valid Company Token
     * @apiAuth query token CompanyToken XXX A valid Company Token
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/management/settings/deleteAll.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Settings::deleteAll
     */
    private static function deleteAll(App $app, callable $auth, callable $permission) {
        $app
            ->delete(
                '/management/settings',
                'App\Controller\Settings:deleteAll'
            )
            ->add($permission(EndpointPermission::PRIVATE_ACTION))
            ->add($auth(Auth::COMPANY))
            ->setName('settings:deleteAll');
    }

    /**
     * Retrieve a single Setting.
     *
     * Retrieves all public information from a Setting.
     *
     * @apiEndpoint GET /management/settings/{section}/{property}
     * @apiGroup Company Settings
     * @apiAuth header token CompanyToken XXX A valid Company Token
     * @apiAuth query token CompanyToken XXX A valid Company Token
     * @apiEndpointURIFragment string section lookup
     * @apiEndpointURIFragment string property username
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/management/settings/getOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Settings::getOne
     */
    private static function getOne(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/management/settings/{settingId:[0-9]+}',
                'App\Controller\Settings:getOne'
            )
            ->add($permission(EndpointPermission::PRIVATE_ACTION))
            ->add($auth(Auth::COMPANY))
            ->setName('settings:getOne');
    }

    /**
     * Update a single Setting.
     *
     * Updates Setting's specific information.
     *
     * @apiEndpoint PUT /management/settings/{section}/{property}
     * @apiGroup Company Settings
     * @apiAuth header token CompanyToken XXX A valid Company Token
     * @apiAuth query token CompanyToken XXX A valid Company Token
     * @apiEndpointURIFragment string section lookup
     * @apiEndpointURIFragment string property username
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/management/settings/updateOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Settings::updateOne
     */
    private static function updateOne(App $app, callable $auth, callable $permission) {
        $app
            ->put(
                '/management/settings/{settingId:[0-9]+}',
                'App\Controller\Settings:updateOne'
            )
            ->add($permission(EndpointPermission::PRIVATE_ACTION))
            ->add($auth(Auth::COMPANY))
            ->setName('settings:updateOne');
    }

    /**
     * Deletes a single Setting.
     *
     * Deletes a single Setting that belongs to the requesting company.
     *
     * @apiEndpoint DELETE /management/settings/{section}/{property}
     * @apiGroup Company Settings
     * @apiAuth header token CompanyToken XXX A valid Company Token
     * @apiAuth query token CompanyToken XXX A valid Company Token
     * @apiEndpointURIFragment string section lookup
     * @apiEndpointURIFragment string property username
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/management/settings/deleteOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Settings::deleteOne
     */
    private static function deleteOne(App $app, callable $auth, callable $permission) {
        $app
            ->delete(
                '/management/settings/{settingId:[0-9]+}',
                'App\Controller\Settings:deleteOne'
            )
            ->add($permission(EndpointPermission::PRIVATE_ACTION))
            ->add($auth(Auth::COMPANY))
            ->setName('settings:deleteOne');
    }
}
