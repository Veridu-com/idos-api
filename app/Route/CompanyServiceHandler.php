<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Route;

use App\Middleware\Auth;
use App\Middleware\Permission;
use Interop\Container\ContainerInterface;
use Slim\App;

/**
 * CompanyServiceHandler routing definitions.
 *
 * @link docs/company-service-handlers/overview.md
 * @see App\Controller\CompanyServiceHandlers
 */
class CompanyServiceHandler implements RouteInterface {
    /**
     * {@inheritdoc}
     */
    public static function getPublicNames() : array {
        return [
            'company-service-handlers:listAll',
            'company-service-handlers:deleteAll',
            'company-service-handlers:createNew',
            'company-service-handlers:getOne',
            'company-service-handlers:deleteOne'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function register(App $app) {
        $app->getContainer()[\App\Controller\CompanyServiceHandlers::class] = function (ContainerInterface $container) {
            return new \App\Controller\CompanyServiceHandlers(
                $container->get('repositoryFactory')->create('CompanyServiceHandler'),
                $container->get('commandBus'),
                $container->get('commandFactory'),
                $container->get('optimus')
            );
        };

        $container              = $app->getContainer();
        $authMiddleware         = $container->get('authMiddleware');
        $permissionMiddleware   = $container->get('permissionMiddleware');

        self::listAll($app, $authMiddleware, $permissionMiddleware);
        self::getOne($app, $authMiddleware, $permissionMiddleware);
        self::createNew($app, $authMiddleware, $permissionMiddleware);
        self::deleteOne($app, $authMiddleware, $permissionMiddleware);
        self::deleteAll($app, $authMiddleware, $permissionMiddleware);
    }

    /**
     * List all Company Service handlers.
     *
     * Retrieve a complete list of company service handlers of the requesting company.
     *
     * @apiEndpoint GET /company-service-handlers
     * @apiGroup Company CompanyServiceHandler
     * @apiAuth header key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiAuth query key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/company-service-handlers/listAll.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\CompanyServiceHandlers::listAll
     */
    private static function listAll(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/company-service-handlers',
                'App\Controller\CompanyServiceHandlers:listAll'
            )
            ->add($permission(Permission::PRIVATE_ACTION))
            ->add($auth(Auth::COMP_PRIVKEY))
            ->setName('company-service-handlers:listAll');
    }

    /**
     * Create new CompanyServiceHandler.
     *
     * Create a new company service handler for the requesting company.
     *
     * @apiEndpoint POST /company-service-handlers
     * @apiGroup Company CompanyServiceHandler
     * @apiAuth header key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiAuth query key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/company-service-handlers/createNew.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\CompanyServiceHandlers::createNew
     */
    private static function createNew(App $app, callable $auth, callable $permission) {
        $app
            ->post(
                '/company-service-handlers',
                'App\Controller\CompanyServiceHandlers:createNew'
            )
            ->add($permission(Permission::PRIVATE_ACTION))
            ->add($auth(Auth::COMP_PRIVKEY))
            ->setName('company-service-handlers:createNew');
    }

    /**
     * Deletes all company-service-handlers.
     *
     * Deletes all company service handlers that belongs to the requesting company.
     *
     * @apiEndpoint DELETE /company-service-handlers
     * @apiGroup Company CompanyServiceHandler
     * @apiAuth header key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiAuth query key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/company-service-handlers/deleteAll.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\CompanyServiceHandlers::deleteAll
     */
    private static function deleteAll(App $app, callable $auth, callable $permission) {
        $app
            ->delete(
                '/company-service-handlers',
                'App\Controller\CompanyServiceHandlers:deleteAll'
            )
            ->add($permission(Permission::PRIVATE_ACTION))
            ->add($auth(Auth::COMP_PRIVKEY))
            ->setName('company-service-handlers:deleteAll');
    }

    /**
     * Retrieve a single Company Service handler.
     *
     * Retrieves all public information from a Company Service handler.
     *
     * @apiEndpoint GET /company-service-handlers/{id}
     * @apiGroup Company CompanyServiceHandler
     * @apiAuth header key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiAuth query key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiEndpointURIFragment  int  id
     * 
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/company-service-handlers/getOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\CompanyServiceHandlers::getOne
     */
    private static function getOne(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/company-service-handlers/{id:[0-9]+}',
                'App\Controller\CompanyServiceHandlers:getOne'
            )
            ->add($permission(Permission::PRIVATE_ACTION))
            ->add($auth(Auth::COMP_PRIVKEY))
            ->setName('company-service-handlers:getOne');
    }

    /**
     * Deletes a single CompanyServiceHandler.
     *
     * Deletes a single Company Service handler that belongs to the requesting company.
     *
     * @apiEndpoint DELETE /company-service-handlers/{id}
     * @apiGroup Company CompanyServiceHandler
     * @apiAuth header key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiAuth query key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiEndpointURIFragment  int  id
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/company-service-handlers/deleteOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\CompanyServiceHandlers::deleteOne
     */
    private static function deleteOne(App $app, callable $auth, callable $permission) {
        $app
            ->delete(
                '/company-service-handlers/{id:[0-9]+}',
                'App\Controller\CompanyServiceHandlers:deleteOne'
            )
            ->add($permission(Permission::PRIVATE_ACTION))
            ->add($auth(Auth::COMP_PRIVKEY))
            ->setName('company-service-handlers:deleteOne');
    }
}
