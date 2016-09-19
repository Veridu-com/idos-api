<?php
/*/*
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
 * Sso routing definitions.
 *
 * @link docs/sso/overview.md
 * @see App\Controller\Sso
 */
class Sso implements RouteInterface {
    /**
     * {@inheritdoc}
     */
    public static function getPublicNames() : array {
        return [
            'sso:listAll',
            'sso:createNew',
            'sso:getOne',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function register(App $app) {
        $app->getContainer()[\App\Controller\Sso::class] = function (ContainerInterface $container) {
            return new \App\Controller\Sso(
                $container->get('repositoryFactory')->create('Setting'),
                $container->get('repositoryFactory')->create('Credential'),
                $container->get('settings'),
                $container->get('commandBus'),
                $container->get('commandFactory')
            );
        };

        $container            = $app->getContainer();
        $authMiddleware       = $container->get('authMiddleware');
        $permissionMiddleware = $container->get('endpointPermissionMiddleware');

        self::listAll($app, $authMiddleware, $permissionMiddleware);
        self::createNew($app, $authMiddleware, $permissionMiddleware);
        self::getOne($app, $authMiddleware, $permissionMiddleware);
    }

    /**
     * List all Sso providers.
     *
     * Retrieve a complete list of all sso providers.
     *
     * @apiEndpoint GET /sso
     * @apiGroup Profile Sso
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/sso/listAll.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Sso::listAll
     */
    private static function listAll(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/sso',
                'App\Controller\Sso:listAll'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::NONE))
            ->setName('sso:listAll');
    }

    /**
     * Create new Feature.
     *
     * Create a new feature for the given user.
     *
     * @apiEndpoint POST profiles/{userName}/features
     * @apiGroup Profile Sso
     * @apiAuth header key credentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential Token
     * @apiAuth query key credentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential Token
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/profile/features/createNew.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Sso::createNew
     */
    private static function createNew(App $app, callable $auth, callable $permission) {
        $app
            ->post(
                '/sso',
                'App\Controller\Sso:createNew'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::NONE))
            ->setName('sso:createNew');
    }

    /**
     * Retrieves the status of a sso provider.
     *
     * @apiEndpoint GET /sso/{providerName}
     * @apiGroup Profile Sso
     * @apiEndpointURIFragment string providerName facebook
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/sso/getOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Sso::getOne
     */
    private static function getOne(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/sso/{providerName:[a-zA-Z0-9]+}',
                'App\Controller\Sso:getOne'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::NONE))
            ->setName('sso:getOne');
    }
}
