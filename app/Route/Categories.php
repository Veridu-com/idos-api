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
 * Company Categories.
 *
 * Company Categories are what allows a company to add tailored functionality to the API in order to assess specific
 * information. If a company wants to support a specific Profile Source, access a certain data point within a Profile,
 * or change the way the API interprets data, Categories are a simple and direct way of doing this.
 *
 * @apiDisabled
 *
 * @link docs/categories/overview.md
 * @see \App\Controller\Categories
 */
class Categories implements RouteInterface {
    /**
     * {@inheritdoc}
     */
    public static function getPublicNames() : array {
        return [
            'category:listAll'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function register(App $app) {
        $app->getContainer()[\App\Controller\Categories::class] = function (ContainerInterface $container) {
            return new \App\Controller\Categories(
                $container
                    ->get('repositoryFactory')
                    ->create('Category'),
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
    }

    /**
     * List all Categories.
     *
     * Retrieves a complete list of all categories.
     *
     * @apiEndpoint GET /categories
     * @apiGroup CompanyDisabled
     * @apiAuth header token IdentityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     * @apiAuth query token identityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/categories/listAll.md
     * @see \App\Middleware\Auth::__invoke
     * @see \App\Middleware\Permission::__invoke
     * @see \App\Controller\Categories::listAll
     */
    private static function listAll(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/system/categories',
                'App\Controller\Categories:listAll'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::IDENTITY))
            ->setName('category:listAll');
    }
}
