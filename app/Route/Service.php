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
 * Service routing definitions.
 *
 * @link docs/services/overview.md
 * @see App\Controller\Services
 */
class Service implements RouteInterface {
    /**
     * {@inheritdoc}
     */
    public static function getPublicNames() : array {
        return [
            'services:listAll',
            'services:deleteAll',
            'services:createNew',
            'services:getOne',
            'services:updateOne',
            'services:deleteOne'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function register(App $app) {
        $app->getContainer()[\App\Controller\Services::class] = function (ContainerInterface $container) {
            return new \App\Controller\Services(
                $container->get('repositoryFactory')->create('Service'),
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
     * List all Services.
     *
     * Retrieve a complete list of services that are visible to the requesting company.
     *
     * @apiEndpoint GET /services
     * @apiGroup Company Service
     * @apiAuth header key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiAuth query key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/services/listAll.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Services::listAll
     */
    private static function listAll(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/services',
                'App\Controller\Services:listAll'
            )
            ->add($permission(CompanyPermission::PUBLIC_ACTION))
            ->add($auth(Auth::COMP_PRIVKEY))
            ->setName('service:listAll');
    }

    /**
     * Create new Service.
     *
     * Create a new service for the requesting company.
     *
     * @apiEndpoint POST /services
     * @apiGroup Company Service
     * @apiAuth header key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiAuth query key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/services/createNew.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Services::createNew
     */
    private static function createNew(App $app, callable $auth, callable $permission) {
        $app
            ->post(
                '/services',
                'App\Controller\Services:createNew'
            )
            ->add($permission(CompanyPermission::PUBLIC_ACTION))
            ->add($auth(Auth::COMP_PRIVKEY))
            ->setName('service:createNew');
    }

    /**
     * Deletes all service.
     *
     * Deletes all services that belongs to the requesting company.
     *
     * @apiEndpoint DELETE /services
     * @apiGroup Company Service
     * @apiAuth header key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiAuth query key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/services/deleteAll.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Services::deleteAll
     */
    private static function deleteAll(App $app, callable $auth, callable $permission) {
        $app
            ->delete(
                '/services',
                'App\Controller\Services:deleteAll'
            )
            ->add($permission(CompanyPermission::PUBLIC_ACTION))
            ->add($auth(Auth::COMP_PRIVKEY))
            ->setName('service:deleteAll');
    }

    /**
     * Retrieve a single Service.
     *
     * Retrieves all public information from a Service.
     *
     * @apiEndpoint GET /services/{serviceId}
     * @apiGroup Company Service
     * @apiAuth header key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiAuth query key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiEndpointURIFragment  int  serviceId 12345
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/services/getOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Services::getOne
     */
    private static function getOne(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/services/{serviceId:[0-9]+}',
                'App\Controller\Services:getOne'
            )
            ->add($permission(CompanyPermission::PUBLIC_ACTION))
            ->add($auth(Auth::COMP_PRIVKEY))
            ->setName('service:getOne');
    }

    /**
     * Update a single Service.
     *
     * Updates Service's specific information.
     *
     * @apiEndpoint PUT /services/{serviceId}
     * @apiGroup Company Service
     * @apiAuth header key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiAuth query key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiEndpointURIFragment int serviceId 1
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/services/updateOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Services::updateOne
     */
    private static function updateOne(App $app, callable $auth, callable $permission) {
        $app
            ->put(
                '/services/{serviceId:[0-9]+}',
                'App\Controller\Services:updateOne'
            )
            ->add($permission(CompanyPermission::PUBLIC_ACTION))
            ->add($auth(Auth::COMP_PRIVKEY))
            ->setName('service:updateOne');
    }

    /**
     * Deletes a single Service.
     *
     * Deletes a single Service that belongs to the requesting company.
     *
     * @apiEndpoint DELETE /services/{serviceId}
     * @apiGroup Company Service
     * @apiAuth header key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiAuth query key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiEndpointURIFragment int serviceId 1
     * 
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/services/deleteOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Services::deleteOne
     */
    private static function deleteOne(App $app, callable $auth, callable $permission) {
        $app
            ->delete(
                '/services/{serviceId:[0-9]+}',
                'App\Controller\Services:deleteOne'
            )
            ->add($permission(CompanyPermission::PUBLIC_ACTION))
            ->add($auth(Auth::COMP_PRIVKEY))
            ->setName('service:deleteOne');
    }
}
