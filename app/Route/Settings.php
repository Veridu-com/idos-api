<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Route;

use App\Middleware\Auth;
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
    public static function getPublicNames() {
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

        $container      = $app->getContainer();
        $authMiddleware = $container->get('authMiddleware');

        self::listAll($app, $authMiddleware);
        self::listAllFromSection($app, $authMiddleware);
        self::deleteAll($app, $authMiddleware);
        self::createNew($app, $authMiddleware);
        self::getOne($app, $authMiddleware);
        self::updateOne($app, $authMiddleware);
        self::deleteOne($app, $authMiddleware);
    }

    /**
     * List all Settings.
     *
     * Retrieve a complete list of all settings that belong to the requesting company.
     *
     * @apiEndpoint GET /companies/{companySlug}/settings
     * @apiGroup Company Settings
     * @apiAuth header key compPrivKey Company's Private Key
     * @apiAuth query key compPrivKey Company's Private Key
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/companies/settings/listAll.md
     *
     * @uses App\Middleware\Auth::__invoke
     *
     * @see App\Controller\Settings::listAll
     */
    private static function listAll(App $app, callable $auth) {
        $app
            ->get(
                '/companies/{companySlug:[a-zA-Z0-9_-]+}/settings',
                'App\Controller\Settings:listAll'
            )
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
     * @apiAuth header key compPrivKey Company's Private Key
     * @apiAuth query key compPrivKey Company's Private Key
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/companies/settings/listAllFromSection.md
     *
     * @uses App\Middleware\Auth::__invoke
     *
     * @see App\Controller\Settings::listAllFromSection
     */
    private static function listAllFromSection(App $app, callable $auth) {
        $app
            ->get(
                '/companies/{companySlug:[a-zA-Z0-9_-]+}/settings/{section:[a-zA-Z0-9_-]+}',
                'App\Controller\Settings:listAllFromSection'
            )
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
     * @apiAuth header key compPrivKey Company's Private Key
     * @apiAuth query key compPrivKey Company's Private Key
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/companies/settings/createNew.md
     *
     * @uses App\Middleware\Auth::__invoke
     *
     * @see App\Controller\Settings::createNew
     */
    private static function createNew(App $app, callable $auth) {
        $app
            ->post(
                '/companies/{companySlug:[a-zA-Z0-9_-]+}/settings',
                'App\Controller\Settings:createNew'
            )
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
     * @apiAuth header key compPrivKey Company's Private Key
     * @apiAuth query key compPrivKey Company's Private Key
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/companies/settings/deleteAll.md
     *
     * @uses App\Middleware\Auth::__invoke
     *
     * @see App\Controller\Settings::deleteAll
     */
    private static function deleteAll(App $app, callable $auth) {
        $app
            ->delete(
                '/companies/{companySlug:[a-zA-Z0-9_-]+}/settings',
                'App\Controller\Settings:deleteAll'
            )
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
     * @apiAuth header key compPrivKey Company's Private Key
     * @apiAuth query key compPrivKey Company's Private Key
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/companies/settings/getOne.md
     *
     * @uses App\Middleware\Auth::__invoke
     *
     * @see App\Controller\Settings::getOne
     */
    private static function getOne(App $app, callable $auth) {
        $app
            ->get(
                '/companies/{companySlug:[a-zA-Z0-9_-]+}/settings/{section:[a-zA-Z0-9_-]+}/{property:[a-zA-Z0-9_-]+}',
                'App\Controller\Settings:getOne'
            )
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
     * @apiAuth header key compPrivKey Company's Private Key
     * @apiAuth query key compPrivKey Company's Private Key
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/companies/settings/updateOne.md
     *
     * @uses App\Middleware\Auth::__invoke
     *
     * @see App\Controller\Settings::updateOne
     */
    private static function updateOne(App $app, callable $auth) {
        $app
            ->put(
                '/companies/{companySlug:[a-zA-Z0-9_-]+}/settings/{section:[a-zA-Z0-9_-]+}/{property:[a-zA-Z0-9_-]+}',
                'App\Controller\Settings:updateOne'
            )
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
     * @apiAuth header key compPrivKey Company's Private Key
     * @apiAuth query key compPrivKey Company's Private Key
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/companies/settings/deleteOne.md
     *
     * @uses App\Middleware\Auth::__invoke
     *
     * @see App\Controller\Settings::deleteOne
     */
    private static function deleteOne(App $app, callable $auth) {
        $app
            ->delete(
                '/companies/{companySlug:[a-zA-Z0-9_-]+}/settings/{section:[a-zA-Z0-9_-]+}/{property:[a-zA-Z0-9_-]+}',
                'App\Controller\Settings:deleteOne'
            )
            ->add($auth(Auth::COMP_PRIVKEY))
            ->setName('settings:deleteOne');
    }
}
