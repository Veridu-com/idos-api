<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Route\Company;

use App\Controller\ControllerInterface;
use App\Entity\Role;
use App\Middleware\Auth;
use App\Middleware\EndpointPermission;
use App\Route\RouteInterface;
use Interop\Container\ContainerInterface;
use Slim\App;

/**
 * Company Subscriptions.
 *
 * Company Subscription is a way of assigning a specific Company to a Gate.
 *
 * @link docs/companies/subscriptions/overview.md
 * @see \App\Controller\Companies
 */
class Subscriptions implements RouteInterface {
    /**
     * {@inheritdoc}
     */
    public static function getPublicNames() : array {
        return [
            'subscriptions:listAll',
            'subscriptions:createNew',
            'subscriptions:deleteOne'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function register(App $app) : void {
        $app->getContainer()[\App\Controller\Company\Subscriptions::class] = function (ContainerInterface $container) : ControllerInterface {
            $repositoryFactory = $container->get('repositoryFactory');

            return new \App\Controller\Company\Subscriptions(
                $repositoryFactory
                    ->create('Company\Subscription'),
                $repositoryFactory
                    ->create('Company\Credential'),
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
        self::createNew($app, $authMiddleware, $permissionMiddleware);
        self::deleteOne($app, $authMiddleware, $permissionMiddleware);
    }

    /**
     * List all Subscriptions.
     *
     * Retrieves a complete list of all subscriptions that belong to the requesting credential.
     *
     * @apiEndpoint GET /companies/{companySlug}/credentials/{pubKey}/subscriptions
     * @apiGroup Company
     * @apiAuth header token IdentityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     * @apiAuth query token identityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     * @apiEndpointURIFragment string companySlug veridu-ltd
     * @apiEndpointURIFragment string pubKey g89d7fg9d87gf9d8fgdfgadasd
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/companies/subscriptions/listAll.md
     * @see \App\Middleware\Auth::__invoke
     * @see \App\Middleware\Permission::__invoke
     * @see \App\Controller\Company\Subscriptions::listAll
     */
    private static function listAll(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/companies/{companySlug:[a-z0-9_-]+}/credentials/{pubKey:[a-zA-Z0-9]+}/subscriptions',
                'App\Controller\Company\Subscriptions:listAll'
            )
            ->add(
                $permission(
                    EndpointPermission::SELF_ACTION | EndpointPermission::PARENT_ACTION,
                    Role::COMPANY_OWNER_BIT | Role::COMPANY_ADMIN_BIT
                )
            )
            ->add($auth(Auth::IDENTITY))
            ->setName('subscriptions:listAll');
    }

    /**
     * Create new Subscription.
     *
     * Creates a new subscription for the requesting company.
     *
     * @apiEndpoint POST /companies/{companySlug}/credentials/{pubKey}/subscriptions
     * @apiGroup Company
     * @apiAuth header token IdentityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     * @apiAuth query token identityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     * @apiEndpointURIFragment string companySlug veridu-ltd
     * @apiEndpointURIFragment string pubKey SDFSDGDHG67567567
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/companies/subscriptions/createNew.md
     * @see \App\Middleware\Auth::__invoke
     * @see \App\Middleware\Permission::__invoke
     * @see \App\Controller\Company\Subscriptions::createNew
     */
    private static function createNew(App $app, callable $auth, callable $permission) {
        $app
            ->post(
                '/companies/{companySlug:[a-z0-9_-]+}/credentials/{pubKey:[a-zA-Z0-9]+}/subscriptions',
                'App\Controller\Company\Subscriptions:createNew'
            )
            ->add(
                $permission(
                    EndpointPermission::SELF_ACTION | EndpointPermission::PARENT_ACTION,
                    Role::COMPANY_OWNER_BIT | Role::COMPANY_ADMIN_BIT
                )
            )
            ->add($auth(Auth::IDENTITY))
            ->setName('subscriptions:createNew');
    }

    /**
     * Deletes a single Subscription.
     *
     * Deletes a single Subscription that belongs to the target company.
     *
     * @apiEndpoint DELETE /companies/{companySlug}/credentials/{pubKey}/subscriptions/{subscriptionId}
     * @apiGroup Company
     * @apiAuth header token IdentityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     * @apiAuth query token identityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     * @apiEndpointURIFragment string companySlug veridu-ltd
     * @apiEndpointURIFragment string pubKey SDFSDGDHG67567567
     * @apiEndpointURIFragment int subscriptionId 321654
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/companies/subscriptions/deleteOne.md
     * @see \App\Middleware\Auth::__invoke
     * @see \App\Middleware\Permission::__invoke
     * @see \App\Controller\Company\Subscriptions::deleteOne
     */
    private static function deleteOne(App $app, callable $auth, callable $permission) {
        $app
            ->delete(
                '/companies/{companySlug:[a-z0-9_-]+}/credentials/{pubKey:[a-zA-Z0-9]+}/subscriptions/{subscriptionId:[0-9]+}',
                'App\Controller\Company\Subscriptions:deleteOne'
            )
            ->add(
                $permission(
                    EndpointPermission::SELF_ACTION | EndpointPermission::PARENT_ACTION,
                    Role::COMPANY_OWNER_BIT | Role::COMPANY_ADMIN_BIT
                )
            )
            ->add($auth(Auth::IDENTITY))
            ->setName('subscriptions:deleteOne');
    }
}
