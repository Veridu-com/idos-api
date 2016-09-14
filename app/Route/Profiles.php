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
 * Profile routing definitions.
 *
 * @link docs/profiles/overview.md
 * @see App\Controller\Profiles
 */
class Profiles implements RouteInterface {
    /**
     * {@inheritdoc}
     */
    public static function getPublicNames() : array {
        return [
            'profile:listAll'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function register(App $app) {
        $app->getContainer()[\App\Controller\Profiles::class] = function (ContainerInterface $container) {
            return new \App\Controller\Profiles(
                $container->get('repositoryFactory')->create('User'),
                $container->get('commandBus'),
                $container->get('commandFactory'),
                $container->get('optimus')
            );
        };

        $container            = $app->getContainer();
        $authMiddleware       = $container->get('authMiddleware');
        $permissionMiddleware = $container->get('endpointPermissionMiddleware');

        self::listAll($app, $authMiddleware, $permissionMiddleware);
    }

    /**
     * List all Profiles.
     *
     * Retrieve a complete list of profiles that are visible to the requesting company.
     *
     * @apiEndpoint GET /profiles
     * @apiGroup Company Profile
<<<<<<< HEAD
     * @apiAuth header token IdentityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     * @apiAuth query token identityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
=======
     * @apiAuth header token IdentityToken XXX A valid Identity Token
     * @apiAuth query token IdentityToken XXX A valid Identity Token
>>>>>>> 38414c0f682f504064149c6715641486b5378a8f
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/profiles/listAll.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Profiles::listAll
     */
    private static function listAll(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/profiles',
                'App\Controller\Profiles:listAll'
            )
            ->add($permission(EndpointPermission::SELF_ACTION))
            ->add($auth(Auth::CREDENTIAL))
            ->setName('profile:listAll');
    }
}
