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
 * @link docs/settings/overview.md
 *
 * @see App\Controller\Settings
 */
class Settings implements RouteInterface {

    /**
     * {@inheritDoc}
     */
    public static function getPublicNames() {
        return [
            'settings:listAll',
            'settings:createNew',
            'settings:deleteAll',
            'settings:getOne',
            'settings:updateOne',
            'settings:deleteOne'
        ];
    }

    /**
     * {@inheritDoc}
     */
    public static function register(App $app) {
        $app->getContainer()[\App\Controller\Settings::class] = function (ContainerInterface $container) {
            return new \App\Controller\Settings(
                $container->get('repositoryFactory')->create('Company'),
                $container->get('commandBus'),
                $container->get('commandFactory'),
                $container->get('optimus')
            );
        };

        $container      = $app->getContainer();
        $authMiddleware = $container->get('authMiddleware');

        self::listAll($app, $authMiddleware);
        self::createNew($app, $authMiddleware);
        self::deleteAll($app, $authMiddleware);
        self::getOne($app, $authMiddleware);
        self::updateOne($app, $authMiddleware);
        self::deleteOne($app, $authMiddleware);
    }

    /**
     * List all Settings
     *
     * Retrieve a complete list of all child settings that belong to the requesting company.
     *
     * @apiEndpoint GET /settings
     * @apiAuth header key compPrivKey Company's Private Key
     * @apiAuth query key compPrivKey Company's Private Key
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/settings/listAll.md
     *
     * @see App\Controller\Settings::listAll
     */
    private static function listAll(App $app, callable $auth) {
        $app
            ->get(
                '/settings',
                'App\Controller\Settings:listAll'
            )
            ->add($auth(Auth::COMP_PRIVKEY))
            ->setName('settings:listAll');
    }

    /**
     * Create new Company
     *
     * Create a new child company for the requesting company.
     *
     * @apiEndpoint POST /settings
     * @apiAuth header key compPrivKey Company's Private Key
     * @apiAuth query key compPrivKey Company's Private Key
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/settings/createNew.md
     *
     * @see App\Controller\Settings::createNew
     */
    private static function createNew(App $app, callable $auth) {
        $app
            ->post(
                '/settings',
                'App\Controller\Settings:createNew'
            )
            ->add($auth(Auth::COMP_PRIVKEY))
            ->setName('settings:createNew');
    }

    /**
     * Delete all Settings
     *
     * Delete all child settings that belong to the requesting company.
     *
     * @apiEndpoint DELETE /settings
     * @apiAuth header key compPrivKey Company's Private Key
     * @apiAuth query key compPrivKey Company's Private Key
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/settings/deleteAll.md
     *
     * @see App\Controller\Settings::deleteAll
     */
    private static function deleteAll(App $app, callable $auth) {
        $app
            ->delete(
                '/settings',
                'App\Controller\Settings:deleteAll'
            )
            ->add($auth(Auth::COMP_PRIVKEY))
            ->setName('settings:deleteAll');
    }

    /**
     * Retrieve a single Company
     *
     * Retrieves all public information from a Company
     *
     * @apiEndpoint GET /settings/{companySlug}
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/settings/getOne.md
     *
     * @see App\Controller\Settings::getOne
     */
    private static function getOne(App $app, callable $auth) {
        $app
            ->get(
                '/settings/{companySlug:[a-zA-Z0-9_-]+}',
                'App\Controller\Settings:getOne'
            )
            ->add($auth(Auth::NONE))
            ->setName('settings:getOne');
    }

    /**
     * Update a single Company
     *
     * Updates Company's specific information
     *
     * @apiEndpoint POST /settings/:companySlug
     * @apiAuth header key compPrivKey Company's Private Key
     * @apiAuth query key compPrivKey Company's Private Key
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/settings/updateOne.md
     *
     * @see App\Controller\Settings::updateOne
     */
    private static function updateOne(App $app, callable $auth) {
        $app
            ->post(
                '/settings/{companySlug:[a-zA-Z0-9_-]+}',
                'App\Controller\Settings:updateOne'
            )
            ->add($auth(Auth::COMP_PRIVKEY))
            ->setName('settings:updateOne');
    }

    /**
     * Deletes a single Company
     *
     * Deletes the requesting company or a child company that belongs to it.
     *
     * @apiEndpoint DELETE /settings/:companySlug
     * @apiAuth header key compPrivKey Company's Private Key
     * @apiAuth query key compPrivKey Company's Private Key
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/settings/deleteOne.md
     *
     * @see App\Controller\Settings::deleteOne
     */
    private static function deleteOne(App $app, callable $auth) {
        $app
            ->delete(
                '/settings/{companySlug:[a-zA-Z0-9_-]+}',
                'App\Controller\Settings:deleteOne'
            )
            ->add($auth(Auth::COMP_PRIVKEY))
            ->setName('settings:deleteOne');
    }
}
