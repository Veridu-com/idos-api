<?php
/*/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Route\Profile;

use App\Controller\ControllerInterface;
use App\Middleware\Auth;
use App\Middleware\EndpointPermission;
use App\Route\RouteInterface;
use Interop\Container\ContainerInterface;
use Slim\App;

/**
 * Profile Recommendation.
 *
 * Our recommendation gives guidance on whether the profile should be accepted for your service.
 * It is a binary true/false result, with a provided reason for that result. The recommendation
 * is based on a rule set created by us with the specifications you provided.
 *
 * @link docs/profile/recommendation/overview.md
 * @see \App\Controller\Profile\Recommendation
 */
class Recommendation implements RouteInterface {
    /**
     * {@inheritdoc}
     */
    public static function getPublicNames() : array {
        return [
            'recommendation:getOne',
            'recommendation:upsert'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function register(App $app) : void {
        $app->getContainer()[\App\Controller\Profile\Recommendation::class] = function (ContainerInterface $container) : ControllerInterface {
            return new \App\Controller\Profile\Recommendation(
                $container->get('repositoryFactory')->create('Profile\Recommendation'),
                $container->get('commandBus'),
                $container->get('commandFactory')
            );
        };

        $container            = $app->getContainer();
        $authMiddleware       = $container->get('authMiddleware');
        $permissionMiddleware = $container->get('endpointPermissionMiddleware');

        self::getOne($app, $authMiddleware, $permissionMiddleware);
        self::upsert($app, $authMiddleware, $permissionMiddleware);
    }

    /**
     * Retrieve the profile recommendation.
     *
     * Retrieves the profile recommendation calculated according to the ruleset specified by the company.
     *
     * @apiEndpoint GET /profiles/{userName}/recommendation
     * @apiGroup Profile
     * @apiAuth header token UserToken|CredentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid User|Credential Token
     * @apiAuth query token userToken|credentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid User|Credential Token
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/profile/recommendation/getOne.md
     * @see \App\Middleware\Auth::__invoke
     * @see \App\Middleware\Permission::__invoke
     * @see \App\Controller\Profile\Recommendation::getOne
     */
    private static function getOne(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/profiles/{userName:[a-zA-Z0-9_-]+}/recommendation',
                'App\Controller\Profile\Recommendation:getOne'
            )
            ->add($permission(EndpointPermission::PRIVATE_ACTION))
            ->add($auth(Auth::USER | Auth::CREDENTIAL))
            ->setName('recommendation:getOne');
    }

    /**
     * Create or update the profile recommendation.
     *
     * Creates or updates the profile recommendation.
     *
     * @apiEndpoint PUT /profiles/{userName}/recommendation
     * @apiGroup Profile
     * @apiAuth header token UserToken|CredentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid User|Credential Token
     * @apiAuth query token userToken|credentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid User|Credential Token
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/profile/recommendation/upsert.md
     * @see \App\Middleware\Auth::__invoke
     * @see \App\Middleware\Permission::__invoke
     * @see \App\Controller\Profile\Recommendation::upsert
     */
    private static function upsert(App $app, callable $auth, callable $permission) {
        $app
            ->put(
                '/profiles/{userName:[a-zA-Z0-9_-]+}/recommendation',
                'App\Controller\Profile\Recommendation:upsert'
            )
            ->add($permission(EndpointPermission::PRIVATE_ACTION))
            ->add($auth(Auth::CREDENTIAL))
            ->setName('recommendation:upsert');
    }
}
