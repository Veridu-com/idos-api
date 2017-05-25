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
 * Profile.
 *
 * The User Profile is all of the data the API has collated from the Raw Data of one User.
 * It contains all of the data points about this User (eg. Attributes, Candidates, Features, Gates,
 * Flags, etc.) and all of their results. This is used to retrieve the complete information about one User
 * once it has been processed by the API.
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
            'profile:getOne'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function register(App $app) : void {
        $app->getContainer()[\App\Controller\Profiles::class] = function (ContainerInterface $container) : ControllerInterface {
            $repositoryFactory = $container->get('repositoryFactory');

            return new \App\Controller\Profiles(
                $repositoryFactory
                    ->create('User'),
                $repositoryFactory
                    ->create('Profile\\Attribute'),
                $repositoryFactory
                    ->create('Profile\\Candidate'),
                $repositoryFactory
                    ->create('Profile\\Score'),
                $repositoryFactory
                    ->create('Profile\\Source'),
                $repositoryFactory
                    ->create('Profile\\Gate'),
                $repositoryFactory
                    ->create('Profile\\Flag'),
                $repositoryFactory
                    ->create('Profile\\Recommendation'),
                $container
                    ->get('commandBus'),
                $container
                    ->get('commandFactory')
            );
        };

        $container            = $app->getContainer();
        $authMiddleware       = $container->get('authMiddleware');
        $permissionMiddleware = $container->get('endpointPermissionMiddleware');

        self::getOne($app, $authMiddleware, $permissionMiddleware);
    }

    /**
     * Retrieve all information of a single profile.
     *
     * Retrieve all profile candidates, attributes, gates and flags.
     *
     * @apiEndpoint GET /profiles/{userName}
     * @apiGroup Profile
     * @apiAuth header token UserToken eyJ0eXAiOiJKV1QiLCJhbGciOiJIU A valid User Token
     * @apiAuth query token userToken eyJ0eXAiOiJKV1QiLCJhbGciOiJIU A valid User Token
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/profiles/getOne.md
     * @see \App\Middleware\Auth::__invoke
     * @see \App\Middleware\Permission::__invoke
     * @see \App\Controller\Profiles::getOne
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
