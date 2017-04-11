<?php
/*
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
 * Profile Attributes.
 *
 * A Profile Attribute is a specific piece of information within a Profile that has been extracted by the API.
 * This Attribute can be simple like a first or second name, or more detailed like a User's hometown or employment.
 * If the API has extracted multiple results for one Attribute, all of them will be listed as Candidates.
 *
 * @link docs/profiles/attribute/overview.md
 * @see \App\Controller\Profile\Attributes
 */
class Attributes implements RouteInterface {
    /**
     * {@inheritdoc}.
     */
    public static function getPublicNames() : array {
        return [
            'attribute:listAll',
            'attribute:getOne'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function register(App $app) {
        $app->getContainer()[\App\Controller\Profile\Attributes::class] = function (ContainerInterface $container) : ControllerInterface {
            return new \App\Controller\Profile\Attributes(
                $container->get('repositoryFactory')
                    ->create('Profile\Attribute'),
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
     * List all profile attributes.
     *
     * Retrieve a complete list of the attributes for a given user.
     *
     * @apiEndpoint GET /profiles/{userName}/attributes
     * @apiGroup Profile
     * @apiAuth header token CredentialToken|UserToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential or User Token
     * @apiAuth query token credentialToken|userToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential or User Token
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/profiles/attributes/listAll.md
     * @see \App\Middleware\Auth::__invoke
     * @see \App\Middleware\Permission::__invoke
     * @see \App\Controller\Profile\Attributes::listAll
     */
    private static function listAll(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/profiles/{userName:[a-zA-Z0-9_-]+}/attributes',
                'App\Controller\Profile\Attributes:listAll'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::USER | Auth::CREDENTIAL))
            ->setName('attribute:listAll');
    }

    /**
     * Retrieve a single attribute.
     *
     * Retrieves all public information from an attribute.
     *
     * @apiEndpoint GET /profiles/{userName}/attributes/{attributeName}
     * @apiGroup Profile
     * @apiAuth header token UserToken|CredentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid User|Credential Token
     * @apiAuth query token UserToken|CredentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid User|Credential Token
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     * @apiEndpointURIFragment string attributeName firstName
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/profiles/attributes/getOne.md
     * @see \App\Middleware\Auth::__invoke
     * @see \App\Middleware\Permission::__invoke
     * @see \App\Controller\Profile\Attributes::getOne
     */
    private static function getOne(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/profiles/{userName:[a-zA-Z0-9_-]+}/attributes/{attributeName:[0-9a-zA-Z]+}',
                'App\Controller\Profile\Attributes:getOne'
            )
            ->add($permission(EndpointPermission::PRIVATE_ACTION))
            ->add($auth(Auth::USER | Auth::CREDENTIAL))
            ->setName('attribute:getOne');
    }
}
