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
 * ServiceHandler routing definitions.
 *
 * @link docs/service-handlers/overview.md
 * @see App\Controller\ServiceHandlers
 */
class ServiceHandler implements RouteInterface {
    /**
     * {@inheritdoc}
     */
    public static function getPublicNames() : array {
        return [
            'service-handlers:listAll',
            'service-handlers:deleteAll',
            'service-handlers:createNew',
            'service-handlers:getOne',
            'service-handlers:updateOne',
            'service-handlers:deleteOne'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function register(App $app) {
        $app->getContainer()[\App\Controller\ServiceHandlers::class] = function (ContainerInterface $container) {
            return new \App\Controller\ServiceHandlers(
                $container->get('repositoryFactory')->create('ServiceHandler'),
                $container->get('commandBus'),
                $container->get('commandFactory'),
                $container->get('optimus')
            );
        };

        $container              = $app->getContainer();
        $authMiddleware         = $container->get('authMiddleware');
        $permissionMiddleware   = $container->get('permissionMiddleware');

        self::listAll($app, $authMiddleware, $permissionMiddleware);
        self::listAllFromService($app, $authMiddleware, $permissionMiddleware);
        self::getOne($app, $authMiddleware, $permissionMiddleware);
        // self::deleteAll($app, $authMiddleware, $permissionMiddleware);
        // self::createNew($app, $authMiddleware, $permissionMiddleware);
        // self::updateOne($app, $authMiddleware, $permissionMiddleware);
        // self::deleteOne($app, $authMiddleware, $permissionMiddleware);
    }

    /**
     * List all ServiceHandler.
     *
     * Retrieve a complete list of service handlers that belong to the requesting company.
     *
     * @apiEndpoint GET /service-handlers
     * @apiGroup Company ServiceHandler
     * @apiAuth header key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiAuth query key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/service-handlers/service-handlers/listAll.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\ServiceHandlers::listAll
     */
    private static function listAll(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/service-handlers',
                'App\Controller\ServiceHandlers:listAll'
            )
            ->add($permission(Permission::PRIVATE_ACTION))
            ->add($auth(Auth::COMP_PRIVKEY))
            ->setName('service-handlers:listAll');
    }

    /**
     * List all ServiceHandler from service.
     *
     * Retrieve a complete list of service handlers that belong to the requesting company.
     *
     * @apiEndpoint GET /service-handlers
     * @apiGroup Company ServiceHandler
     * @apiAuth header key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiAuth query key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/service-handlers/service-handlers/listAll.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\ServiceHandlers::listAll
     */
    private static function listAllFromService(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/service-handlers/{serviceSlug:[a-zA-Z0-9_-]+}',
                'App\Controller\ServiceHandlers:listAllFromService'
            )
            ->add($permission(Permission::PUBLIC_ACTION))
            ->add($auth(Auth::COMP_PRIVKEY))
            ->setName('service-handlers:listAllFromService');
    }

    /**
     * Create new ServiceHandler.
     *
     * Create a new credential for the requesting company.
     *
     * @apiEndpoint POST /service-handlers/{companySlug}/service-handlers
     * @apiGroup Company ServiceHandler
     * @apiAuth header key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiAuth query key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiEndpointURIFragment string companySlug veridu-ltd
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/service-handlers/service-handlers/createNew.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\ServiceHandlers::createNew
     */
    private static function createNew(App $app, callable $auth, callable $permission) {
        $app
            ->post(
                '/service-handlers/{companySlug:[a-zA-Z0-9_-]+}/service-handlers',
                'App\Controller\ServiceHandlers:createNew'
            )
            ->add($permission(Permission::PRIVATE_ACTION))
            ->add($auth(Auth::COMP_PRIVKEY))
            ->setName('service-handlers:createNew');
    }

    /**
     * Deletes all service-handlers.
     *
     * Deletes all service-handlers that belongs to the requesting company.
     *
     * @apiEndpoint DELETE /service-handlers/{companySlug}/service-handlers
     * @apiGroup Company ServiceHandler
     * @apiAuth header key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiAuth query key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiEndpointURIFragment string companySlug veridu-ltd
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/service-handlers/service-handlers/deleteAll.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\ServiceHandlers::deleteAll
     */
    private static function deleteAll(App $app, callable $auth, callable $permission) {
        $app
            ->delete(
                '/service-handlers/{companySlug:[a-zA-Z0-9_-]+}/service-handlers',
                'App\Controller\ServiceHandlers:deleteAll'
            )
            ->add($permission(Permission::PRIVATE_ACTION))
            ->add($auth(Auth::COMP_PRIVKEY))
            ->setName('service-handlers:deleteAll');
    }

    /**
     * Retrieve a single ServiceHandler.
     *
     * Retrieves all public information from a ServiceHandler.
     *
     * @apiEndpoint GET /service-handlers/{companySlug}/service-handlers/{section}/{property}
     * @apiGroup Company ServiceHandler
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
     * @link docs/service-handlers/service-handlers/getOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\ServiceHandlers::getOne
     */
    private static function getOne(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/service-handlers/{serviceSlug:[a-zA-Z0-9_-]+}/{serviceHandlerSlug:[a-zA-Z0-9_-]+}',
                'App\Controller\ServiceHandlers:getOne'
            )
            ->add($permission(Permission::PRIVATE_ACTION))
            ->add($auth(Auth::COMP_PRIVKEY))
            ->setName('service-handlers:getOne');
    }

    /**
     * Update a single ServiceHandler.
     *
     * Updates ServiceHandler's specific information.
     *
     * @apiEndpoint PUT /service-handlers/{companySlug}/service-handlers/{section}/{property}
     * @apiGroup Company ServiceHandler
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
     * @link docs/service-handlers/service-handlers/updateOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\ServiceHandlers::updateOne
     */
    private static function updateOne(App $app, callable $auth, callable $permission) {
        $app
            ->put(
                '/service-handlers/{companySlug:[a-zA-Z0-9_-]+}/service-handlers/{section:[a-zA-Z0-9_-]+}/{property:[a-zA-Z0-9_-]+}',
                'App\Controller\ServiceHandlers:updateOne'
            )
            ->add($permission(Permission::PRIVATE_ACTION))
            ->add($auth(Auth::COMP_PRIVKEY))
            ->setName('service-handlers:updateOne');
    }

    /**
     * Deletes a single ServiceHandler.
     *
     * Deletes a single ServiceHandler that belongs to the requesting company.
     *
     * @apiEndpoint DELETE /service-handlers/{companySlug}/service-handlers/{section}/{property}
     * @apiGroup Company ServiceHandler
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
     * @link docs/service-handlers/service-handlers/deleteOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\ServiceHandlers::deleteOne
     */
    private static function deleteOne(App $app, callable $auth, callable $permission) {
        $app
            ->delete(
                '/service-handlers/{companySlug:[a-zA-Z0-9_-]+}/service-handlers/{section:[a-zA-Z0-9_-]+}/{property:[a-zA-Z0-9_-]+}',
                'App\Controller\ServiceHandlers:deleteOne'
            )
            ->add($permission(Permission::PRIVATE_ACTION))
            ->add($auth(Auth::COMP_PRIVKEY))
            ->setName('service-handlers:deleteOne');
    }
}
