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
 * @see \App\Controller\Profiles
 */
class Profiles implements RouteInterface {
    /**
     * {@inheritdoc}
     */
    public static function getPublicNames() : array {
        return [
            'profile:listAll',
            'profile:getOne'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function register(App $app) {
        $app->getContainer()[\App\Controller\Profiles::class] = function (ContainerInterface $container) {
            return new \App\Controller\Profiles(
                $container->get('repositoryFactory')->create('User'),
                $container->get('repositoryFactory')->create('Profile\Candidate'),
                $container->get('repositoryFactory')->create('Profile\Score'),
                $container->get('repositoryFactory')->create('Profile\Source'),
                $container->get('commandBus'),
                $container->get('commandFactory')
            );
        };

        $container            = $app->getContainer();
        $authMiddleware       = $container->get('authMiddleware');
        $permissionMiddleware = $container->get('endpointPermissionMiddleware');

        self::listAll($app, $authMiddleware, $permissionMiddleware);
        self::getOne($app, $authMiddleware, $permissionMiddleware);
    }

    /**
     * List all Profiles.
     *
     * Retrieve a complete list of profiles that are visible to the requesting company.
     *
     * @apiEndpoint GET /profiles
     * @apiGroup Profile
     * @apiAuth header token CredentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential Token
     * @apiAuth query token credentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential Token
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/profiles/listAll.md
     * @see \App\Middleware\Auth::__invoke
     * @see \App\Middleware\Permission::__invoke
     * @see \App\Controller\Profiles::listAll
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

    /**
     * List all information of a single profile.
     *
     * Retrieve a complete list of profiles that are visible to the requesting company.
     *
     * @apiEndpoint GET /profiles
     * @apiGroup Company Profile
     * @apiAuth header token IdentityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     * @apiAuth query token identityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/profiles/listAll.md
     * @see \App\Middleware\Auth::__invoke
     * @see \App\Middleware\Permission::__invoke
     * @see \App\Controller\Profiles::listAll
     */
    private static function getOne(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/profiles/{userName:[a-zA-Z0-9_-]+}',
                'App\Controller\Profiles:getOne'
            )
            ->add($permission(EndpointPermission::SELF_ACTION))
            ->add($auth(Auth::USER))
            ->setName('profile:getOne');
    }
}
