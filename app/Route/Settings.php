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
            'settings:listAllFromSection',
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

        $container              = $app->getContainer();
        $authMiddleware         = $container->get('authMiddleware');
        $permissionMiddleware   = $container->get('companyPermissionMiddleware');

        self::listAll($app, $authMiddleware, $permissionMiddleware);
        self::listAllFromSection($app, $authMiddleware, $permissionMiddleware);
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
     * @apiEndpoint GET /companies/{companySlug}/settings
     * @apiGroup Company Settings
     * @apiAuth header key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiAuth query key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiEndpointURIFragment string companySlug veridu-ltd
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/companies/settings/listAll.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Settings::listAll
     */
    private static function listAll(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/companies/{companySlug:[a-zA-Z0-9_-]+}/settings',
                'App\Controller\Settings:listAll'
            )
            ->add($permission(CompanyPermission::PRIVATE_ACTION))
            ->add($auth(Auth::COMP_PRIVKEY))
            ->setName('settings:listAll');
    }
    /**
     * List all Settings from section.
     *
     * Retrieve a complete list of all settings that belong to the requesting company and has the given section.
     *
     * @apiEndpoint GET /companies/{companySlug}/settings/{section}
     * @apiGroup Company Settings
     * @apiAuth header key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiAuth query key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiEndpointURIFragment string companySlug veridu-ltd
     * @apiEndpointURIFragment string section lookup
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/companies/settings/listAllFromSection.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Settings::listAllFromSection
     */
    private static function listAllFromSection(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/companies/{companySlug:[a-zA-Z0-9_-]+}/settings/{section:[a-zA-Z0-9_-]+}',
                'App\Controller\Settings:listAllFromSection'
            )
            ->add($permission(CompanyPermission::PRIVATE_ACTION))
            ->add($auth(Auth::COMP_PRIVKEY))
            ->setName('settings:listAllFromSection');
    }

    /**
     * Create new Setting.
     *
     * Create a new credential for the requesting company.
     *
     * @apiEndpoint POST /companies/{companySlug}/settings
     * @apiGroup Company Settings
     * @apiAuth header key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiAuth query key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiEndpointURIFragment string companySlug veridu-ltd
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/companies/settings/createNew.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Settings::createNew
     */
    private static function createNew(App $app, callable $auth, callable $permission) {
        $app
            ->post(
                '/companies/{companySlug:[a-zA-Z0-9_-]+}/settings',
                'App\Controller\Settings:createNew'
            )
            ->add($permission(CompanyPermission::PRIVATE_ACTION))
            ->add($auth(Auth::COMP_PRIVKEY))
            ->setName('settings:createNew');
    }

    /**
     * Deletes all settings.
     *
     * Deletes all settings that belongs to the requesting company.
     *
     * @apiEndpoint DELETE /companies/{companySlug}/settings
     * @apiGroup Company Settings
     * @apiAuth header key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiAuth query key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiEndpointURIFragment string companySlug veridu-ltd
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/companies/settings/deleteAll.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Settings::deleteAll
     */
    private static function deleteAll(App $app, callable $auth, callable $permission) {
        $app
            ->delete(
                '/companies/{companySlug:[a-zA-Z0-9_-]+}/settings',
                'App\Controller\Settings:deleteAll'
            )
            ->add($permission(CompanyPermission::PRIVATE_ACTION))
            ->add($auth(Auth::COMP_PRIVKEY))
            ->setName('settings:deleteAll');
    }

    /**
     * Retrieve a single Setting.
     *
     * Retrieves all public information from a Setting.
     *
     * @apiEndpoint GET /companies/{companySlug}/settings/{section}/{property}
     * @apiGroup Company Settings
     * @apiAuth header key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiAuth query key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiEndpointURIFragment string companySlug veridu-ltd
     * @apiEndpointURIFragment string section lookup
     * @apiEndpointURIFragment string property username
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/companies/settings/getOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Settings::getOne
     */
    private static function getOne(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/companies/{companySlug:[a-zA-Z0-9_-]+}/settings/{section:[a-zA-Z0-9_-]+}/{property:[a-zA-Z0-9_-]+}',
                'App\Controller\Settings:getOne'
            )
            ->add($permission(CompanyPermission::PRIVATE_ACTION))
            ->add($auth(Auth::COMP_PRIVKEY))
            ->setName('settings:getOne');
    }

    /**
     * Update a single Setting.
     *
     * Updates Setting's specific information.
     *
     * @apiEndpoint PUT /companies/{companySlug}/settings/{section}/{property}
     * @apiGroup Company Settings
     * @apiAuth header key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiAuth query key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiEndpointURIFragment string companySlug veridu-ltd
     * @apiEndpointURIFragment string section lookup
     * @apiEndpointURIFragment string property username
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/companies/settings/updateOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Settings::updateOne
     */
    private static function updateOne(App $app, callable $auth, callable $permission) {
        $app
            ->put(
                '/companies/{companySlug:[a-zA-Z0-9_-]+}/settings/{section:[a-zA-Z0-9_-]+}/{property:[a-zA-Z0-9_-]+}',
                'App\Controller\Settings:updateOne'
            )
            ->add($permission(CompanyPermission::PRIVATE_ACTION))
            ->add($auth(Auth::COMP_PRIVKEY))
            ->setName('settings:updateOne');
    }

    /**
     * Deletes a single Setting.
     *
     * Deletes a single Setting that belongs to the requesting company.
     *
     * @apiEndpoint DELETE /companies/{companySlug}/settings/{section}/{property}
     * @apiGroup Company Settings
     * @apiAuth header key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiAuth query key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiEndpointURIFragment string companySlug veridu-ltd
     * @apiEndpointURIFragment string section lookup
     * @apiEndpointURIFragment string property username
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/companies/settings/deleteOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Settings::deleteOne
     */
    private static function deleteOne(App $app, callable $auth, callable $permission) {
        $app
            ->delete(
                '/companies/{companySlug:[a-zA-Z0-9_-]+}/settings/{section:[a-zA-Z0-9_-]+}/{property:[a-zA-Z0-9_-]+}',
                'App\Controller\Settings:deleteOne'
            )
            ->add($permission(CompanyPermission::PRIVATE_ACTION))
            ->add($auth(Auth::COMP_PRIVKEY))
            ->setName('settings:deleteOne');
    }
}
