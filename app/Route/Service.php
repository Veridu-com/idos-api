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
 * Company Services.
 *
 * Company Services are what allows a company to add tailored functionality to the API in order to assess specific
 * information. If a company wants to support a specific Profile Source, access a certain data point within a Profile,
 * or change the way the API interprets data, Services are a simple and direct way of doing this.
 *
 * @apiDisabled
 *
 * @link docs/services/overview.md
 * @see \App\Controller\Services
 */
class Service implements RouteInterface {
    /**
     * {@inheritdoc}
     */
    public static function getPublicNames() : array {
        return [
            'service:listAll',
            'service:deleteAll',
            'service:createNew',
            'service:getOne',
            'service:updateOne',
            'service:deleteOne'
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
                $container->get('commandFactory')
            );
        };

        $container            = $app->getContainer();
        $authMiddleware       = $container->get('authMiddleware');
        $permissionMiddleware = $container->get('endpointPermissionMiddleware');

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
     * Retrieves a complete list of all services.
     *
     * @apiEndpoint GET /companies/{companySlug}/services
     * @apiGroup Company
     * @apiAuth header token IdentityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     * @apiAuth query token identityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/services/listAll.md
     * @see \App\Middleware\Auth::__invoke
     * @see \App\Middleware\Permission::__invoke
     * @see \App\Controller\Services::listAll
     */
    private static function listAll(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/companies/{companySlug:[a-z0-9_-]+}/services',
                'App\Controller\Services:listAll'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::IDENTITY))
            ->setName('service:listAll');
    }

    /**
     * Retrieve a single Service.
     *
     * Retrieves all public information from a Service.
     *
     * @apiEndpoint GET /companies/{companySlug}/services/{serviceId}
     * @apiGroup Company
     * @apiAuth header token IdentityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     * @apiAuth query token identityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     * @apiEndpointURIFragment  int  serviceId 1234
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/services/getOne.md
     * @see \App\Middleware\Auth::__invoke
     * @see \App\Middleware\Permission::__invoke
     * @see \App\Controller\Services::getOne
     */
    private static function getOne(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/companies/{companySlug:[a-z0-9_-]+}/services/{serviceId:[0-9]+}',
                'App\Controller\Services:getOne'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::IDENTITY))
            ->setName('service:getOne');
    }

    /**
     * Create new Service.
     *
     * Create a new service for the requesting company.
     *
     * @apiEndpoint POST /companies/{companySlug}/services
     * @apiGroup Company
     * @apiAuth header token IdentityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     * @apiAuth query token identityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/services/createNew.md
     * @see \App\Middleware\Auth::__invoke
     * @see \App\Middleware\Permission::__invoke
     * @see \App\Controller\Services::createNew
     */
    private static function createNew(App $app, callable $auth, callable $permission) {
        $app
            ->post(
                '/companies/{companySlug:[a-z0-9_-]+}/services',
                'App\Controller\Services:createNew'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::IDENTITY))
            ->setName('service:createNew');
    }

    /**
     * Update a single Service.
     *
     * Updates Service's specific information.
     *
     * @apiEndpoint PUT /companies/{companySlug}/services/{serviceId}
     * @apiGroup Company
     * @apiAuth header token IdentityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     * @apiAuth query token identityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     * @apiEndpointURIFragment int serviceId 1234
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/services/updateOne.md
     * @see \App\Middleware\Auth::__invoke
     * @see \App\Middleware\Permission::__invoke
     * @see \App\Controller\Services::updateOne
     */
    private static function updateOne(App $app, callable $auth, callable $permission) {
        $app
            ->put(
                '/companies/{companySlug:[a-z0-9_-]+}/services/{serviceId:[0-9]+}',
                'App\Controller\Services:updateOne'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::IDENTITY))
            ->setName('service:updateOne');
    }

    /**
     * Deletes a single Service.
     *
     * Deletes a single Service that belongs to the requesting company.
     *
     * @apiEndpoint DELETE /companies/{companySlug}/services/{serviceId}
     * @apiGroup Company
     * @apiAuth header token IdentityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     * @apiAuth query token identityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     * @apiEndpointURIFragment int serviceId 1234
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/services/deleteOne.md
     * @see \App\Middleware\Auth::__invoke
     * @see \App\Middleware\Permission::__invoke
     * @see \App\Controller\Services::deleteOne
     */
    private static function deleteOne(App $app, callable $auth, callable $permission) {
        $app
            ->delete(
                '/companies/{companySlug:[a-z0-9_-]+}/services/{serviceId:[0-9]+}',
                'App\Controller\Services:deleteOne'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::IDENTITY))
            ->setName('service:deleteOne');
    }

    /**
     * Deletes all service.
     *
     * Deletes all services that belongs to the requesting company.
     *
     * @apiEndpoint DELETE /companies/{companySlug}/services
     * @apiGroup Company
     * @apiAuth header token IdentityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     * @apiAuth query token identityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/services/deleteAll.md
     * @see \App\Middleware\Auth::__invoke
     * @see \App\Middleware\Permission::__invoke
     * @see \App\Controller\Services::deleteAll
     */
    private static function deleteAll(App $app, callable $auth, callable $permission) {
        // FIXME This should be removed!
        $app
            ->delete(
                '/companies/{companySlug:[a-z0-9_-]+}/services',
                'App\Controller\Services:deleteAll'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::IDENTITY))
            ->setName('service:deleteAll');
    }
}
