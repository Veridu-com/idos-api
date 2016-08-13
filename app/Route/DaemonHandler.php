<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Route;

use App\Middleware\Auth;
use App\Middleware\CompanyPermission;
use Interop\Container\ContainerInterface;
use Slim\App;

/**
 * DaemonHandler routing definitions.
 *
 * @link docs/daemon-handlers/overview.md
 * @see App\Controller\DaemonHandlers
 */
class DaemonHandler implements RouteInterface {
    /**
     * {@inheritdoc}
     */
    public static function getPublicNames() : array {
        return [
            'daemon-handlers:listAll',
            'daemon-handlers:deleteAll',
            'daemon-handlers:createNew',
            'daemon-handlers:getOne',
            'daemon-handlers:updateOne',
            'daemon-handlers:deleteOne'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function register(App $app) {
        $app->getContainer()[\App\Controller\DaemonHandlers::class] = function (ContainerInterface $container) {
            return new \App\Controller\DaemonHandlers(
                $container->get('repositoryFactory')->create('DaemonHandler'),
                $container->get('commandBus'),
                $container->get('commandFactory'),
                $container->get('optimus')
            );
        };

        $container              = $app->getContainer();
        $authMiddleware         = $container->get('authMiddleware');
        $permissionMiddleware   = $container->get('companyPermissionMiddleware');

        self::listAll($app, $authMiddleware, $permissionMiddleware);
        self::getOne($app, $authMiddleware, $permissionMiddleware);
        self::createNew($app, $authMiddleware, $permissionMiddleware);
        self::deleteOne($app, $authMiddleware, $permissionMiddleware);
        self::deleteAll($app, $authMiddleware, $permissionMiddleware);
        self::updateOne($app, $authMiddleware, $permissionMiddleware);
    }

    /**
     * List all Daemon handlers.
     *
     * Retrieve a complete list of daemon handlers that belong to the requesting company.
     *
     * @apiEndpoint GET /daemon-handlers
     * @apiGroup Company DaemonHandler
     * @apiAuth header key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiAuth query key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/daemon-handlers/listAll.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\DaemonHandlers::listAll
     */
    private static function listAll(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/daemon-handlers',
                'App\Controller\DaemonHandlers:listAll'
            )
            ->add($permission(CompanyPermission::PRIVATE_ACTION))
            ->add($auth(Auth::COMP_PRIVKEY))
            ->setName('daemon-handlers:listAll');
    }

    /**
     * Create new DaemonHandler.
     *
     * Create a new daemon handler for the requesting company.
     *
     * @apiEndpoint POST /daemon-handlers
     * @apiGroup Company DaemonHandler
     * @apiAuth header key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiAuth query key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/daemon-handlers/createNew.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\DaemonHandlers::createNew
     */
    private static function createNew(App $app, callable $auth, callable $permission) {
        $app
            ->post(
                '/daemon-handlers',
                'App\Controller\DaemonHandlers:createNew'
            )
            ->add($permission(CompanyPermission::PRIVATE_ACTION))
            ->add($auth(Auth::COMP_PRIVKEY))
            ->setName('daemon-handlers:createNew');
    }

    /**
     * Deletes all daemon-handlers.
     *
     * Deletes all daemon handlers that belongs to the requesting company.
     *
     * @apiEndpoint DELETE /daemon-handlers
     * @apiGroup Company DaemonHandler
     * @apiAuth header key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiAuth query key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiEndpointURIFragment string companySlug veridu-ltd
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/daemon-handlers/deleteAll.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\DaemonHandlers::deleteAll
     */
    private static function deleteAll(App $app, callable $auth, callable $permission) {
        $app
            ->delete(
                '/daemon-handlers',
                'App\Controller\DaemonHandlers:deleteAll'
            )
            ->add($permission(CompanyPermission::PRIVATE_ACTION))
            ->add($auth(Auth::COMP_PRIVKEY))
            ->setName('daemon-handlers:deleteAll');
    }

    /**
     * Retrieve a single Daemon handler.
     *
     * Retrieves all public information from a Daemon handler.
     *
     * @apiEndpoint GET /daemon-handlers/{companySlug}/daemon-handlers/{section}/{property}
     * @apiGroup Company DaemonHandler
     * @apiAuth header key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiAuth query key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiEndpointURIFragment  string  daemonSlug         email
     * @apiEndpointURIFragment  string  daemonHandlerSlug  veridu-email-daemon-handler
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/daemon-handlers/getOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\DaemonHandlers::getOne
     */
    private static function getOne(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/daemon-handlers/{daemonHandlerId:[a-zA-Z0-9_-]+}',
                'App\Controller\DaemonHandlers:getOne'
            )
            ->add($permission(CompanyPermission::PRIVATE_ACTION))
            ->add($auth(Auth::COMP_PRIVKEY))
            ->setName('daemon-handlers:getOne');
    }

    /**
     * Update a single DaemonHandler.
     *
     * Updates DaemonHandler's specific information.
     *
     * @apiEndpoint PUT /daemon-handlers/{companySlug}/daemon-handlers/{section}/{property}
     * @apiGroup Company DaemonHandler
     * @apiAuth header key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiAuth query key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiEndpointURIFragment  string  daemonSlug         email
     * @apiEndpointURIFragment  string  daemonHandlerSlug  veridu-email-daemon-handler
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/daemon-handlers/updateOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\DaemonHandlers::updateOne
     */
    private static function updateOne(App $app, callable $auth, callable $permission) {
        $app
            ->put(
                '/daemon-handlers/{daemonHandlerId:[a-zA-Z0-9_-]+}',
                'App\Controller\DaemonHandlers:updateOne'
            )
            ->add($permission(CompanyPermission::PRIVATE_ACTION))
            ->add($auth(Auth::COMP_PRIVKEY))
            ->setName('daemon-handlers:updateOne');
    }

    /**
     * Deletes a single DaemonHandler.
     *
     * Deletes a single Daemon handler that belongs to the requesting company.
     *
     * @apiEndpoint DELETE /daemon-handlers/{companySlug}/daemon-handlers/{section}/{property}
     * @apiGroup Company DaemonHandler
     * @apiAuth header key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiAuth query key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiEndpointURIFragment  string  daemonSlug         email
     * @apiEndpointURIFragment  string  daemonHandlerSlug  veridu-email-daemon-handler
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/daemon-handlers/deleteOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\DaemonHandlers::deleteOne
     */
    private static function deleteOne(App $app, callable $auth, callable $permission) {
        $app
            ->delete(
                '/daemon-handlers/{daemonHandlerId:[a-zA-Z0-9_-]+}',
                'App\Controller\DaemonHandlers:deleteOne'
            )
            ->add($permission(CompanyPermission::PRIVATE_ACTION))
            ->add($auth(Auth::COMP_PRIVKEY))
            ->setName('daemon-handlers:deleteOne');
    }
}
