<?php

/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Route;

use App\Middleware\Auth;
use App\Middleware\CompanyPermission;
use Interop\Container\ContainerInterface;
use Slim\App;

/**
 * Hooks routing definitions.
 *
 * @link docs/management/hooks/overview.md
 * @see App\Controller\Hooks
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
        $app->getContainer()[\App\Controller\Hooks::class] = function (ContainerInterface $container) {
            return new \App\Controller\Hooks(
                $container->get('repositoryFactory')->create('Hook'),
                $container->get('repositoryFactory')->create('Credential'),
                $container->get('commandBus'),
                $container->get('commandFactory'),
                $container->get('optimus')
            );
        };

        $container            = $app->getContainer();
        $authMiddleware       = $container->get('authMiddleware');
        $permissionMiddleware = $container->get('companyPermissionMiddleware');

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
     * Retrieve a complete list of all hooks that belong to the requesting credential.
     *
     * @apiEndpoint GET /management/credentials/{pubKey}/hooks
     * @apiGroup Company Hooks
     * @apiAuth header key credentialToken 2f476be4f457ef606f3b9177b5bf19c9 Company's credential token
     * @apiAuth query key credentialToken 2f476be4f457ef606f3b9177b5bf19c9 Company's credential token
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
     * @see App\Controller\Hooks::listAll
     */
    private static function listAll(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/management/credentials/{pubKey:[a-zA-Z0-9]+}/hooks',
                'App\Controller\Hooks:listAll'
            )
            ->add($permission(CompanyPermission::SELF_ACTION))
            ->add($auth(Auth::CRED_TOKEN))
            ->setName('hooks:listAll');
    }
    /**
     * Creates new hook.
     *
     * Creates a new hook for the requesting credential.
     *
     * @apiEndpoint POST /management/credentials/{pubKey}/hooks
     * @apiGroup Company Hooks
     * @apiAuth header key credentialToken 2f476be4f457ef606f3b9177b5bf19c9 Company's credential token
     * @apiAuth query key credentialToken 2f476be4f457ef606f3b9177b5bf19c9 Company's credential token
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
     * @see App\Controller\Hooks::createNew
     */
    private static function createNew(App $app, callable $auth, callable $permission) {
        $app
            ->post(
                '/management/credentials/{pubKey:[a-zA-Z0-9]+}/hooks',
                'App\Controller\Hooks:createNew'
            )
            ->add($permission(CompanyPermission::SELF_ACTION))
            ->add($auth(Auth::CRED_TOKEN))
            ->setName('hooks:createNew');
    }

    /**
     * Update a single hook.
     *
     * Updates a hook that belongs to the requesting credential.
     *
     * @apiEndpoint PUT /management/credentials/{pubKey}/hooks/{hookId}
     * @apiGroup Company Hooks
     * @apiAuth header key credentialToken 2f476be4f457ef606f3b9177b5bf19c9 Company's credential token
     * @apiAuth query key credentialToken 2f476be4f457ef606f3b9177b5bf19c9 Company's credential token
     * @apiEndpointURIFragment string hookId
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
     * @see App\Controller\Hooks::updateOne
     */
    private static function updateOne(App $app, callable $auth, callable $permission) {
        $app
            ->put(
                '/management/credentials/{pubKey:[a-zA-Z0-9]+}/hooks/{hookId:[0-9]+}',
                'App\Controller\Hooks:updateOne'
            )
            ->add($permission(CompanyPermission::SELF_ACTION))
            ->add($auth(Auth::CRED_TOKEN))
            ->setName('hooks:updateOne');
    }

    /**
     * Delete all Hooks.
     *
     * Delete all hooks that belong to the requesting company.
     *
     * @apiEndpoint DELETE /management/credentials/{pubKey}/hooks
     * @apiGroup Company Hooks
     * @apiAuth header key credentialToken 2f476be4f457ef606f3b9177b5bf19c9 Company's credential token
     * @apiAuth query key credentialToken 2f476be4f457ef606f3b9177b5bf19c9 Company's credential token
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
     * @see App\Controller\Hooks::deleteAll
     */
    private static function deleteAll(App $app, callable $auth, callable $permission) {
        $app
            ->delete(
                '/management/credentials/{pubKey:[a-zA-Z0-9]+}/hooks',
                'App\Controller\Hooks:deleteAll'
            )
            ->add($permission(CompanyPermission::SELF_ACTION))
            ->add($auth(Auth::CRED_TOKEN))
            ->setName('hooks:deleteAll');
    }

    /*
     * Retrieve a single hook.
     *
     * Retrieves all public information from a hook
     *
     * @apiEndpoint GET /management/credentials/{pubKey}/hooks/{hookId}
     * @apiGroup Company Hooks
     * @apiAuth header key credentialToken 2f476be4f457ef606f3b9177b5bf19c9 Company's credential token
     * @apiAuth query key credentialToken 2f476be4f457ef606f3b9177b5bf19c9 Company's credential token
     * @apiEndpointURIFragment string hookId
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
     * @see App\Controller\Hooks::getOne
     */
    private static function getOne(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/management/credentials/{pubKey:[a-zA-Z0-9]+}/hooks/{hookId}',
                'App\Controller\Hooks:getOne'
            )
            ->add($permission(CompanyPermission::SELF_ACTION))
            ->add($auth(Auth::CRED_TOKEN))
            ->setName('hooks:getOne');
    }

    /*
     * Deletes a single hook.
     *
     * Deletes a hook that belongs to the requesting credential.
     *
     * @apiEndpoint DELETE /management/credentials/{pubKey}/hooks/{hookId}
     * @apiGroup Company Hooks
     * @apiAuth header key credentialToken 2f476be4f457ef606f3b9177b5bf19c9 Company's credential token
     * @apiAuth query key credentialToken 2f476be4f457ef606f3b9177b5bf19c9 Company's credential token
     * @apiEndpointURIFragment string hookId
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
     * @see App\Controller\Hooks::deleteOne
     */
    private static function deleteOne(App $app, callable $auth, callable $permission) {
        $app
            ->delete(
                '/management/credentials/{pubKey:[a-zA-Z0-9]+}/hooks/{hookId}',
                'App\Controller\Hooks:deleteOne'
            )
            ->add($permission(CompanyPermission::SELF_ACTION))
            ->add($auth(Auth::CRED_TOKEN))
            ->setName('hooks:deleteOne');
    }
}
