<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Route\Company;

use App\Entity\Role;
use App\Middleware\Auth;
use App\Middleware\EndpointPermission;
use App\Route\RouteInterface;
use Interop\Container\ContainerInterface;
use Slim\App;

/**
 * Hooks routing definitions.
 *
 * @link docs/management/hooks/overview.md
 * @see App\Controller\Company\Hooks
 */
class Hooks implements RouteInterface {
    /**
     * {@inheritdoc}
     */
    public static function getPublicNames() : array {
        return [
            'hooks:listAll',
            'hooks:createNew',
            'hooks:deleteAll',
            'hooks:getOne',
            'hooks:updateOne',
            'hooks:deleteOne'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function register(App $app) {
        $app->getContainer()[\App\Controller\Company\Hooks::class] = function (ContainerInterface $container) {
            return new \App\Controller\Company\Hooks(
                $container->get('repositoryFactory')->create('Company\Hook'),
                $container->get('repositoryFactory')->create('Company\Credential'),
                $container->get('commandBus'),
                $container->get('commandFactory')
            );
        };

        $container            = $app->getContainer();
        $authMiddleware       = $container->get('authMiddleware');
        $permissionMiddleware = $container->get('endpointPermissionMiddleware');

        self::listAll($app, $authMiddleware, $permissionMiddleware);
        self::createNew($app, $authMiddleware, $permissionMiddleware);
        self::deleteAll($app, $authMiddleware, $permissionMiddleware);
        self::getOne($app, $authMiddleware, $permissionMiddleware);
        self::updateOne($app, $authMiddleware, $permissionMiddleware);
        self::deleteOne($app, $authMiddleware, $permissionMiddleware);
    }

    /**
     * List all Hooks.
     *
     * Retrieves a complete list of all hooks that belong to the requesting credential.
     *
     * @apiEndpoint GET /companies/{companySlug}/credentials/{pubKey}/hooks
     * @apiGroup Company Hooks
     * @apiAuth header token CredentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential Token
     * @apiAuth query token credentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential Token
     * @apiEndpointURIFragment string companySlug veridu-ltd
     * @apiEndpointURIFragment string pubKey 8b5fe9db84e338b424ed6d59da3254a0
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/management/hooks/listAll.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Company\Hooks::listAll
     */
    private static function listAll(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/companies/{companySlug:[a-z0-9_-]+}/credentials/{pubKey:[a-zA-Z0-9]+}/hooks',
                'App\Controller\Company\Hooks:listAll'
            )
            ->add(
                $permission(
                EndpointPermission::SELF_ACTION,
                Role::COMPANY_OWNER_BIT | Role::COMPANY_ADMIN_BIT
                )
            )
            ->add($auth(Auth::IDENTITY))
            ->setName('hooks:listAll');
    }
    /**
     * Create new hook.
     *
     * Create a new hook for the requesting credential.
     *
     * @apiEndpoint POST /companies/{companySlug}/credentials/{pubKey}/hooks
     * @apiGroup Company Hooks
     * @apiAuth header token CredentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential Token
     * @apiAuth query token credentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential Token
     * @apiEndpointURIFragment string companySlug veridu-ltd
     * @apiEndpointURIFragment string pubKey 8b5fe9db84e338b424ed6d59da3254a0
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/management/hooks/createNew.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Company\Hooks::createNew
     */
    private static function createNew(App $app, callable $auth, callable $permission) {
        $app
            ->post(
                '/companies/{companySlug:[a-z0-9_-]+}/credentials/{pubKey:[a-zA-Z0-9]+}/hooks',
                'App\Controller\Company\Hooks:createNew'
            )
            ->add(
                $permission(
                EndpointPermission::SELF_ACTION,
                Role::COMPANY_OWNER_BIT | Role::COMPANY_ADMIN_BIT
                )
            )
            ->add($auth(Auth::IDENTITY))
            ->setName('hooks:createNew');
    }

    /**
     * Update a single hook.
     *
     * Updates a hook that belongs to the requesting credential.
     *
     * @apiEndpoint PUT /companies/{companySlug}/credentials/{pubKey}/hooks/{hookId}
     * @apiGroup Company Hooks
     * @apiAuth header token CredentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential Token
     * @apiAuth query token credentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential Token
     * @apiEndpointURIFragment string companySlug veridu-ltd
     * @apiEndpointURIFragment string pubKey 8b5fe9db84e338b424ed6d59da3254a0
     * @apiEndpointURIFragment int hookId 1
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/management/hooks/updateOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Company\Hooks::updateOne
     */
    private static function updateOne(App $app, callable $auth, callable $permission) {
        $app
            ->put(
                '/companies/{companySlug:[a-z0-9_-]+}/credentials/{pubKey:[a-zA-Z0-9]+}/hooks/{hookId:[0-9]+}',
                'App\Controller\Company\Hooks:updateOne'
            )
            ->add(
                $permission(
                EndpointPermission::SELF_ACTION,
                Role::COMPANY_OWNER_BIT | Role::COMPANY_ADMIN_BIT
                )
            )
            ->add($auth(Auth::IDENTITY))
            ->setName('hooks:updateOne');
    }

    /**
     * Delete all Hooks.
     *
     * Deletes all hooks that belong to the requesting company.
     *
     * @apiEndpoint DELETE /companies/{companySlug}/credentials/{pubKey}/hooks
     * @apiGroup Company Hooks
     * @apiAuth header token CredentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential Token
     * @apiAuth query token credentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential Token
     * @apiEndpointURIFragment string pubKey 8b5fe9db84e338b424ed6d59da3254a0
     * @apiEndpointURIFragment string companySlug veridu-ltd
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/management/hooks/deleteAll.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Company\Hooks::deleteAll
     */
    private static function deleteAll(App $app, callable $auth, callable $permission) {
        $app
            ->delete(
                '/companies/{companySlug}/credentials/{pubKey:[a-zA-Z0-9]+}/hooks',
                'App\Controller\Hooks:deleteAll'
            )
            ->add(
                $permission(
                EndpointPermission::SELF_ACTION,
                Role::COMPANY_OWNER_BIT | Role::COMPANY_ADMIN_BIT
                )
            )
            ->add($auth(Auth::IDENTITY))
            ->setName('hooks:deleteAll');
    }

    /**
     * Retrieve a single hook.
     *
     * Retrieves all public information from a hook
     *
     * @apiEndpoint GET /companies/{companySlug}/credentials/{pubKey}/hooks/{hookId}
     * @apiGroup Company Hooks
     * @apiAuth header token CredentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential Token
     * @apiAuth query token credentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential Token
     * @apiEndpointURIFragment string companySlug veridu-ltd
     * @apiEndpointURIFragment string pubKey 8b5fe9db84e338b424ed6d59da3254a0
     * @apiEndpointURIFragment int hookId 1
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/management/hooks/getOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Company\Hooks::getOne
     */
    private static function getOne(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/companies/{companySlug:[a-z0-9_-]+}/credentials/{pubKey:[a-zA-Z0-9]+}/hooks/{hookId:[0-9]+}',
                'App\Controller\Company\Hooks:getOne'
            )
            ->add(
                $permission(
                EndpointPermission::SELF_ACTION,
                Role::COMPANY_OWNER_BIT | Role::COMPANY_ADMIN_BIT
                )
            )
            ->add($auth(Auth::IDENTITY))
            ->setName('hooks:getOne');
    }

    /**
     * Delete a single hook.
     *
     * Deletes a hook that belongs to the requesting credential.
     *
     * @apiEndpoint DELETE /companies/{companySlug}/credentials/{pubKey}/hooks/{hookId}
     * @apiGroup Company Hooks
     * @apiAuth header token CredentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential Token
     * @apiAuth query token credentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential Token
     * @apiEndpointURIFragment string companySlug veridu-ltd
     * @apiEndpointURIFragment string pubKey 8b5fe9db84e338b424ed6d59da3254a0
     * @apiEndpointURIFragment int hookId 1
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/management/hooks/deleteOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Company\Hooks::deleteOne
     */
    private static function deleteOne(App $app, callable $auth, callable $permission) {
        $app
            ->delete(
                '/companies/{companySlug:[a-z0-9_-]+}/credentials/{pubKey:[a-zA-Z0-9]+}/hooks/{hookId:[0-9]+}',
                'App\Controller\Company\Hooks:deleteOne'
            )
            ->add(
                $permission(
                EndpointPermission::SELF_ACTION,
                Role::COMPANY_OWNER_BIT | Role::COMPANY_ADMIN_BIT
                )
            )
            ->add($auth(Auth::IDENTITY))
            ->setName('hooks:deleteOne');
    }
}
