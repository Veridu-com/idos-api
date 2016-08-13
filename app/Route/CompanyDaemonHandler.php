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
 * CompanyDaemonHandler routing definitions.
 *
 * @link docs/company-daemon-handlers/overview.md
 * @see App\Controller\CompanyDaemonHandlers
 */
class CompanyDaemonHandler implements RouteInterface {
    /**
     * {@inheritdoc}
     */
    public static function getPublicNames() : array {
        return [
            'company-daemon-handlers:listAll',
            'company-daemon-handlers:deleteAll',
            'company-daemon-handlers:createNew',
            'company-daemon-handlers:getOne',
            'company-daemon-handlers:deleteOne'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function register(App $app) {
        $app->getContainer()[\App\Controller\CompanyDaemonHandlers::class] = function (ContainerInterface $container) {
            return new \App\Controller\CompanyDaemonHandlers(
                $container->get('repositoryFactory')->create('CompanyDaemonHandler'),
                $container->get('commandBus'),
                $container->get('commandFactory'),
                $container->get('optimus')
            );
        };

        $container                     = $app->getContainer();
        $authMiddleware                = $container->get('authMiddleware');
        $companyPermissionMiddleware   = $container->get('companyPermissionMiddleware');

        self::listAll($app, $authMiddleware, $companyPermissionMiddleware);
        self::getOne($app, $authMiddleware, $companyPermissionMiddleware);
        self::createNew($app, $authMiddleware, $companyPermissionMiddleware);
        self::deleteOne($app, $authMiddleware, $companyPermissionMiddleware);
        self::deleteAll($app, $authMiddleware, $companyPermissionMiddleware);
    }

    /**
     * List all Company Daemon handlers.
     *
     * Retrieve a complete list of company daemon handlers of the requesting company.
     *
     * @apiEndpoint GET /company-daemon-handlers
     * @apiGroup Company CompanyDaemonHandler
     * @apiAuth header key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiAuth query key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/company-daemon-handlers/listAll.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\CompanyPermission::__invoke
     * @see App\Controller\CompanyDaemonHandlers::listAll
     */
    private static function listAll(App $app, callable $auth, callable $companyPermission) {
        $app
            ->get(
                '/company-daemon-handlers',
                'App\Controller\CompanyDaemonHandlers:listAll'
            )
            ->add($companyPermission(CompanyPermission::PRIVATE_ACTION))
            ->add($auth(Auth::COMP_PRIVKEY))
            ->setName('company-daemon-handlers:listAll');
    }

    /**
     * Create new CompanyDaemonHandler.
     *
     * Create a new company daemon handler for the requesting company.
     *
     * @apiEndpoint POST /company-daemon-handlers
     * @apiGroup Company CompanyDaemonHandler
     * @apiAuth header key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiAuth query key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/company-daemon-handlers/createNew.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\CompanyPermission::__invoke
     * @see App\Controller\CompanyDaemonHandlers::createNew
     */
    private static function createNew(App $app, callable $auth, callable $companyPermission) {
        $app
            ->post(
                '/company-daemon-handlers',
                'App\Controller\CompanyDaemonHandlers:createNew'
            )
            ->add($companyPermission(CompanyPermission::PRIVATE_ACTION))
            ->add($auth(Auth::COMP_PRIVKEY))
            ->setName('company-daemon-handlers:createNew');
    }

    /**
     * Deletes all company-daemon-handlers.
     *
     * Deletes all company daemon handlers that belongs to the requesting company.
     *
     * @apiEndpoint DELETE /company-daemon-handlers
     * @apiGroup Company CompanyDaemonHandler
     * @apiAuth header key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiAuth query key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/company-daemon-handlers/deleteAll.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\CompanyPermission::__invoke
     * @see App\Controller\CompanyDaemonHandlers::deleteAll
     */
    private static function deleteAll(App $app, callable $auth, callable $companyPermission) {
        $app
            ->delete(
                '/company-daemon-handlers',
                'App\Controller\CompanyDaemonHandlers:deleteAll'
            )
            ->add($companyPermission(CompanyPermission::PRIVATE_ACTION))
            ->add($auth(Auth::COMP_PRIVKEY))
            ->setName('company-daemon-handlers:deleteAll');
    }

    /**
     * Retrieve a single Company Daemon handler.
     *
     * Retrieves all public information from a Company Daemon handler.
     *
     * @apiEndpoint GET /company-daemon-handlers/{id}
     * @apiGroup Company CompanyDaemonHandler
     * @apiAuth header key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiAuth query key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiEndpointURIFragment  int  id
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/company-daemon-handlers/getOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\CompanyPermission::__invoke
     * @see App\Controller\CompanyDaemonHandlers::getOne
     */
    private static function getOne(App $app, callable $auth, callable $companyPermission) {
        $app
            ->get(
                '/company-daemon-handlers/{companyDaemonHandlerId:[0-9]+}',
                'App\Controller\CompanyDaemonHandlers:getOne'
            )
            ->add($companyPermission(CompanyPermission::PRIVATE_ACTION))
            ->add($auth(Auth::COMP_PRIVKEY))
            ->setName('company-daemon-handlers:getOne');
    }

    /**
     * Deletes a single CompanyDaemonHandler.
     *
     * Deletes a single Company Daemon handler that belongs to the requesting company.
     *
     * @apiEndpoint DELETE /company-daemon-handlers/{id}
     * @apiGroup Company CompanyDaemonHandler
     * @apiAuth header key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiAuth query key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiEndpointURIFragment  int  id
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/company-daemon-handlers/deleteOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\CompanyPermission::__invoke
     * @see App\Controller\CompanyDaemonHandlers::deleteOne
     */
    private static function deleteOne(App $app, callable $auth, callable $companyPermission) {
        $app
            ->delete(
                '/company-daemon-handlers/{id:[0-9]+}',
                'App\Controller\CompanyDaemonHandlers:deleteOne'
            )
            ->add($companyPermission(CompanyPermission::PRIVATE_ACTION))
            ->add($auth(Auth::COMP_PRIVKEY))
            ->setName('company-daemon-handlers:deleteOne');
    }
}
