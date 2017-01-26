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
 * Company HandlerServices.
 *
 * Company HandlerServices are what allows a company to add tailored functionality to the API in order to assess specific
 * information. If a company wants to support a specific Profile Source, access a certain data point within a Profile,
 * or change the way the API interprets data, HandlerServices are a simple and direct way of doing this.
 *
 * @apiDisabled
 *
 * @link docs/handler-services/overview.md
 * @see \App\Controller\HandlerServices
 */
class HandlerService implements RouteInterface {
    /**
     * {@inheritdoc}
     */
    public static function getPublicNames() : array {
        return [
            'handler-services:listAll',
            'handler-services:getOne',
            'handler-services:createNew',
            'handler-services:updateOne',
            'handler-services:deleteOne',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function register(App $app) {
        $app->getContainer()[\App\Controller\HandlerServices::class] = function (ContainerInterface $container) {
            return new \App\Controller\HandlerServices(
                $container->get('repositoryFactory')->create('HandlerService'),
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
        self::updateOne($app, $authMiddleware, $permissionMiddleware);
        self::deleteOne($app, $authMiddleware, $permissionMiddleware);
    }

    /**
     * List all HandlerServices.
     *
     * Retrieves a complete list of all handler-services.
     *
     * @apiEndpoint GET /companies/{companySlug}/handlers/{handlerId}/handler-services
     * @apiGroup Company
     * @apiAuth header token IdentityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     * @apiAuth query token identityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     * @apiEndpointURIFragment string companySlug veridu-ltd
     * @apiEndpointURIFragment int handlerId 1234
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/handler-services/listAll.md
     * @see \App\Middleware\Auth::__invoke
     * @see \App\Middleware\Permission::__invoke
     * @see \App\Controller\HandlerServices::listAll
     */
    private static function listAll(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/companies/{companySlug:[a-z0-9_-]+}/handlers/{handlerId:[0-9]+}/handler-services',
                'App\Controller\HandlerServices:listAll'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::IDENTITY))
            ->setName('handler-services:listAll');
    }

    /**
     * Retrieve a single Handler Service.
     *
     * Retrieves all public information from a Handler Service.
     *
     * @apiEndpoint GET /companies/{companySlug}/handlers/{handlerId}/handler-services/{handlerServiceId}
     * @apiGroup Company
     * @apiAuth header token IdentityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     * @apiAuth query token identityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     * @apiEndpointURIFragment string companySlug veridu-ltd
     * @apiEndpointURIFragment int handlerId 1234
     * @apiEndpointURIFragment int handlerServiceId 9564
     * 
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/handler-services/getOne.md
     * @see \App\Middleware\Auth::__invoke
     * @see \App\Middleware\Permission::__invoke
     * @see \App\Controller\HandlerServices::getOne
     */
    private static function getOne(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/companies/{companySlug:[a-z0-9_-]+}/handlers/{handlerId:[0-9]+}/handler-services/{handlerServiceId:[0-9]+}',
                'App\Controller\HandlerServices:getOne'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::IDENTITY))
            ->setName('handler-services:getOne');
    }

    /**
     * Create new Handler Service.
     *
     * Create a new handler-services for the requesting company.
     *
     * @apiEndpoint POST /companies/{companySlug}/handlers/{handlerId}/handler-services
     * @apiGroup Company
     * @apiAuth header token IdentityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     * @apiAuth query token identityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     * @apiEndpointURIFragment string companySlug veridu-ltd
     * @apiEndpointURIFragment int handlerId 1234
     * 
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/handler-services/createNew.md
     * @see \App\Middleware\Auth::__invoke
     * @see \App\Middleware\Permission::__invoke
     * @see \App\Controller\HandlerServices::createNew
     */
    private static function createNew(App $app, callable $auth, callable $permission) {
        $app
            ->post(
                '/companies/{companySlug:[a-z0-9_-]+}/handlers/{handlerId:[0-9]+}/handler-services',
                'App\Controller\HandlerServices:createNew'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::IDENTITY))
            ->setName('handler-services:createNew');
    }

    /**
     * Update a single Handler Service.
     *
     * Updates Handler Service's specific information.
     *
     * @apiEndpoint PATCH /companies/{companySlug}/handlers/{handlerId}/handler-services/{handlerServiceId}
     * @apiGroup Company
     * @apiAuth header token IdentityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     * @apiAuth query token identityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     * @apiEndpointURIFragment string companySlug veridu-ltd
     * @apiEndpointURIFragment int handlerId 1234
     * @apiEndpointURIFragment int handlerServiceId 9564
     * 
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/handler-services/updateOne.md
     * @see \App\Middleware\Auth::__invoke
     * @see \App\Middleware\Permission::__invoke
     * @see \App\Controller\HandlerServices::updateOne
     */
    private static function updateOne(App $app, callable $auth, callable $permission) {
        $app
            ->patch(
                '/companies/{companySlug:[a-z0-9_-]+}/handlers/{handlerId:[0-9]+}/handler-services/{handlerServiceId:[0-9]+}',
                'App\Controller\HandlerServices:updateOne'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::IDENTITY))
            ->setName('handler-services:updateOne');
    }

    /**
     * Deletes a single Handler Service.
     *
     * Deletes a single Handler Service that belongs to the requesting company.
     *
     * @apiEndpoint DELETE /companies/{companySlug}/handlers/{handlerId}/handler-services/{handlerServiceId}
     * @apiGroup Company
     * @apiAuth header token IdentityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     * @apiAuth query token identityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     * @apiEndpointURIFragment string companySlug veridu-ltd
     * @apiEndpointURIFragment int handlerId 1234
     * @apiEndpointURIFragment int handlerServiceId 9564
     * 
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/handler-services/deleteOne.md
     * @see \App\Middleware\Auth::__invoke
     * @see \App\Middleware\Permission::__invoke
     * @see \App\Controller\HandlerServices::deleteOne
     */
    private static function deleteOne(App $app, callable $auth, callable $permission) {
        $app
            ->delete(
                '/companies/{companySlug:[a-z0-9_-]+}/handlers/{handlerId:[0-9]+}/handler-services/{handlerServiceId:[0-9]+}',
                'App\Controller\HandlerServices:deleteOne'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::IDENTITY))
            ->setName('handler-services:deleteOne');
    }
}
