<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Route;

use App\Controller\ControllerInterface;
use App\Middleware\Auth;
use App\Middleware\EndpointPermission;
use Interop\Container\ContainerInterface;
use Slim\App;

/**
 * Company Handlers.
 *
 * Company Handlers are what allows a company to add tailored functionality to the API in order to assess specific
 * information. A Company Handler has "Services" that can be triggered within idOS processes.
 *
 * @apiDisabled
 *
 * @link docs/handlers/overview.md
 * @see \App\Controller\Handlers
 */
class Handler implements RouteInterface {
    /**
     * {@inheritdoc}
     */
    public static function getPublicNames() : array {
        return [
            'handler:listAll',
            'handler:createNew',
            'handler:getOne',
            'handler:updateOne',
            'handler:deleteOne'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function register(App $app) : void {
        $app->getContainer()[\App\Controller\Handlers::class] = function (ContainerInterface $container) : ControllerInterface {
            return new \App\Controller\Handlers(
                $container
                    ->get('repositoryFactory')
                    ->create('Handler'),
                $container
                    ->get('commandBus'),
                $container
                    ->get('commandFactory')
            );
        };

        $container            = $app->getContainer();
        $authMiddleware       = $container->get('authMiddleware');
        $permissionMiddleware = $container->get('endpointPermissionMiddleware');

        self::listAll($app, $authMiddleware, $permissionMiddleware);
        self::getOne($app, $authMiddleware, $permissionMiddleware);
        self::createNew($app, $authMiddleware, $permissionMiddleware);
        self::deleteOne($app, $authMiddleware, $permissionMiddleware);
        self::updateOne($app, $authMiddleware, $permissionMiddleware);
    }

    /**
     * List all Handlers.
     *
     * Retrieves a complete list of all handlers.
     *
     * @apiEndpoint GET /companies/{companySlug}/handlers
     * @apiGroup Company
     * @apiAuth header token IdentityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     * @apiAuth query token identityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     * @apiEndpointURIFragment
     *
     * @param \Slim\App $app
     * @param callable  $auth
     * @param callable  $permission
     *
     * @return void
     *
     * @link docs/handlers/listAll.md
     * @see \App\Middleware\Auth::__invoke
     * @see \App\Middleware\Permission::__invoke
     * @see \App\Controller\Handlers::listAll
     */
    private static function listAll(App $app, callable $auth, callable $permission) : void {
        $app
            ->get(
                '/companies/{companySlug:[a-z0-9_-]+}/handlers',
                'App\Controller\Handlers:listAll'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::IDENTITY))
            ->setName('handler:listAll');
    }

    /**
     * Retrieve a single Handler.
     *
     * Retrieves all public information from a Handler.
     *
     * @apiEndpoint GET /companies/{companySlug}/handlers/{handlerId}
     * @apiGroup Company
     * @apiAuth header token IdentityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     * @apiAuth query token identityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     * @apiEndpointURIFragment  int  handlerId 1234
     *
     * @param \Slim\App $app
     * @param callable  $auth
     * @param callable  $permission
     *
     * @return void
     *
     * @link docs/handlers/getOne.md
     * @see \App\Middleware\Auth::__invoke
     * @see \App\Middleware\Permission::__invoke
     * @see \App\Controller\Handlers::getOne
     */
    private static function getOne(App $app, callable $auth, callable $permission) : void {
        $app
            ->get(
                '/companies/{companySlug:[a-z0-9_-]+}/handlers/{handlerId:[0-9]+}',
                'App\Controller\Handlers:getOne'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::IDENTITY))
            ->setName('handler:getOne');
    }

    /**
     * Create new Handler.
     *
     * Create a new handler for the requesting company.
     *
     * @apiEndpoint POST /companies/{companySlug}/handlers
     * @apiGroup Company
     * @apiAuth header token IdentityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     * @apiAuth query token identityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     *
     * @param \Slim\App $app
     * @param callable  $auth
     * @param callable  $permission
     *
     * @return void
     *
     * @link docs/handlers/createNew.md
     * @see \App\Middleware\Auth::__invoke
     * @see \App\Middleware\Permission::__invoke
     * @see \App\Controller\Handlers::createNew
     */
    private static function createNew(App $app, callable $auth, callable $permission) : void {
        $app
            ->post(
                '/companies/{companySlug:[a-z0-9_-]+}/handlers',
                'App\Controller\Handlers:createNew'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::IDENTITY))
            ->setName('handler:createNew');
    }

    /**
     * Update a single Handler.
     *
     * Updates Handler's specific information.
     *
     * @apiEndpoint PUT /companies/{companySlug}/handlers/{handlerId}
     * @apiGroup Company
     * @apiAuth header token IdentityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     * @apiAuth query token identityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     * @apiEndpointURIFragment int handlerId 1234
     *
     * @param \Slim\App $app
     * @param callable  $auth
     * @param callable  $permission
     *
     * @return void
     *
     * @link docs/handlers/updateOne.md
     * @see \App\Middleware\Auth::__invoke
     * @see \App\Middleware\Permission::__invoke
     * @see \App\Controller\Handlers::updateOne
     */
    private static function updateOne(App $app, callable $auth, callable $permission) : void {
        $app
            ->patch(
                '/companies/{companySlug:[a-z0-9_-]+}/handlers/{handlerId:[0-9]+}',
                'App\Controller\Handlers:updateOne'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::IDENTITY))
            ->setName('handler:updateOne');
    }

    /**
     * Deletes a single Handler.
     *
     * Deletes a single Handler that belongs to the requesting company.
     *
     * @apiEndpoint DELETE /companies/{companySlug}/handlers/{handlerId}
     * @apiGroup Company
     * @apiAuth header token IdentityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     * @apiAuth query token identityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     * @apiEndpointURIFragment int handlerId 1234
     *
     * @param \Slim\App $app
     * @param callable  $auth
     * @param callable  $permission
     *
     * @return void
     *
     * @link docs/handlers/deleteOne.md
     * @see \App\Middleware\Auth::__invoke
     * @see \App\Middleware\Permission::__invoke
     * @see \App\Controller\Handlers::deleteOne
     */
    private static function deleteOne(App $app, callable $auth, callable $permission) : void {
        $app
            ->delete(
                '/companies/{companySlug:[a-z0-9_-]+}/handlers/{handlerId:[0-9]+}',
                'App\Controller\Handlers:deleteOne'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::IDENTITY))
            ->setName('handler:deleteOne');
    }
}
