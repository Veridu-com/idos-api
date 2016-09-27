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
 * Score routing definitions.
 *
 * @link docs/token/overview.md
 * @see App\Controller\Tokens
 */
class Tokens implements RouteInterface {
    /**
     * {@inheritdoc}
     */
    public static function getPublicNames() : array {
        return [
            'token:exchange'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function register(App $app) {
        $app->getContainer()[\App\Controller\Tokens::class] = function (ContainerInterface $container) {
            return new \App\Controller\Tokens(
                $container->get('repositoryFactory')->create('Company'),
                $container->get('commandBus'),
                $container->get('commandFactory')
            );
        };

        $container            = $app->getContainer();
        $authMiddleware       = $container->get('authMiddleware');
        $permissionMiddleware = $container->get('endpointPermissionMiddleware');

        self::exchange($app, $authMiddleware, $permissionMiddleware);
    }

    /**
     * Exchange a user token by a company token.
     *
     * Exchange a user token by a company token.
     *
     * @apiEndpoint GET /token
     * @apiGroup Token
     * @apiAuth header token User wqxehuwqwsthwosjbxwwsqwsdi User's Token
     * @apiAuth query token user wqxehuwqwsthwosjbxwwsqwsdi User's Token
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     * @apiEndpointURIFragment string attributeName firstName
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/attributes/token/exchange.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Tokens::exchange
     */
    private static function exchange(App $app, callable $auth, callable $permission) {
        $app
            ->post(
                '/token',
                'App\Controller\Tokens:exchange'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::USER))
            ->setName('token:exchange');
    }
}
