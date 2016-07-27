<?php

/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Route;

use App\Middleware\Auth;
use App\Middleware\Permission;
use Interop\Container\ContainerInterface;
use Slim\App;

/**
 * Credentials routing definitions.
 *
 * @link docs/companies/credentials/overview.md
 * @see App\Controller\Companies
 */
class Credentials implements RouteInterface {
    /**
     * {@inheritdoc}
     */
    public static function getPublicNames() : array {
        return [
            'credentials:listAll',
            'credentials:createNew',
            'credentials:deleteAll',
            'credentials:getOne',
            'credentials:updateOne',
            'credentials:deleteOne'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function register(App $app) {
        $app->getContainer()[\App\Controller\Credentials::class] = function (ContainerInterface $container) {
            return new \App\Controller\Credentials(
                $container->get('repositoryFactory')->create('Credential'),
                $container->get('commandBus'),
                $container->get('commandFactory'),
                $container->get('optimus')
            );
        };

        $container            = $app->getContainer();
        $authMiddleware       = $container->get('authMiddleware');
        $permissionMiddleware = $container->get('permissionMiddleware');

        self::listAll($app, $authMiddleware, $permissionMiddleware);
        self::createNew($app, $authMiddleware, $permissionMiddleware);
        self::deleteAll($app, $authMiddleware, $permissionMiddleware);
        self::getOne($app, $authMiddleware, $permissionMiddleware);
        self::updateOne($app, $authMiddleware, $permissionMiddleware);
        self::deleteOne($app, $authMiddleware, $permissionMiddleware);
    }

    /**
     * List all Credentials.
     *
     * Retrieve a complete list of all credentials that belong to the requesting company.
     *
     * @apiEndpoint GET /companies/{companySlug}/credentials
     * @apiGroup Company Credentials
     * @apiAuth header key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiAuth query key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiEndpointURIFragment string companySlug veridu-ltd
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/companies/credentials/listAll.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Credentials::listAll
     */
    private static function listAll(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/companies/{companySlug:[a-zA-Z0-9_-]+}/credentials',
                'App\Controller\Credentials:listAll'
            )
            ->add($permission(Permission::PRIVATE_ACTION))
            ->add($auth(Auth::COMP_PRIVKEY))
            ->setName('credentials:listAll');
    }

    /**
     * Create new Credential.
     *
     * Create a new credential for the requesting company.
     *
     * @apiEndpoint POST /companies/{companySlug}/credentials
     * @apiGroup Company Credentials
     * @apiAuth header key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiAuth query key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiEndpointURIFragment string companySlug veridu-ltd
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/companies/credentials/createNew.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Credentials::createNew
     */
    private static function createNew(App $app, callable $auth, callable $permission) {
        $app
            ->post(
                '/companies/{companySlug:[a-zA-Z0-9_-]+}/credentials',
                'App\Controller\Credentials:createNew'
            )
            ->add($permission(Permission::PRIVATE_ACTION))
            ->add($auth(Auth::COMP_PRIVKEY))
            ->setName('credentials:createNew');
    }

    /**
     * Delete All Credentials.
     *
     * Delete all credentials that belong to the requesting company.
     *
     * @apiEndpoint DELETE /companies/{companySlug}/credentials
     * @apiGroup Company Credentials
     * @apiAuth header key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiAuth query key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiEndpointURIFragment string companySlug veridu-ltd
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/companies/credentials/deleteAll.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Credentials::deleteAll
     */
    private static function deleteAll(App $app, callable $auth, callable $permission) {
        $app
            ->delete(
                '/companies/{companySlug:[a-zA-Z0-9_-]+}/credentials',
                'App\Controller\Credentials:deleteAll'
            )
            ->add($permission(Permission::PRIVATE_ACTION))
            ->add($auth(Auth::COMP_PRIVKEY))
            ->setName('credentials:deleteAll');
    }

    /**
     * Retrieve a single Credential.
     *
     * Retrieves all public information from a Credential
     *
     * @apiEndpoint GET /companies/{companySlug}/credentials/{pubKey}
     * @apiGroup Company Credentials
     * @apiAuth header key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiAuth query key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiEndpointURIFragment string companySlug veridu-ltd
     * @apiEndpointURIFragment string pubKey FEDCBA
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/companies/credentials/getOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Credentials::getOne
     */
    private static function getOne(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/companies/{companySlug:[a-zA-Z0-9_-]+}/credentials/{pubKey:[a-zA-Z0-9]+}',
                'App\Controller\Credentials:getOne'
            )
            ->add($permission(Permission::PRIVATE_ACTION))
            ->add($auth(Auth::COMP_PRIVKEY))
            ->setName('credentials:getOne');
    }

    /**
     * Update a single Credential.
     *
     * Updates Credential's specific information
     *
     * @apiEndpoint PUT /companies/{companySlug}/credentials/{pubKey}
     * @apiGroup Company Credentials
     * @apiAuth header key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiAuth query key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiEndpointURIFragment string companySlug veridu-ltd
     * @apiEndpointURIFragment string pubKey FEDCBA
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/companies/credentials/updateOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Credentials::updateOne
     */
    private static function updateOne(App $app, callable $auth, callable $permission) {
        $app
            ->put(
                '/companies/{companySlug:[a-zA-Z0-9_-]+}/credentials/{pubKey:[a-zA-Z0-9]+}',
                'App\Controller\Credentials:updateOne'
            )
            ->add($permission(Permission::PRIVATE_ACTION))
            ->add($auth(Auth::COMP_PRIVKEY))
            ->setName('credentials:updateOne');
    }

    /**
     * Deletes a single Credential.
     *
     * Deletes a single Credential that belongs to the requesting company.
     *
     * @apiEndpoint DELETE /companies/{companySlug}/credentials/{pubKey}
     * @apiGroup Company Credentials
     * @apiAuth header key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiAuth query key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiEndpointURIFragment string companySlug veridu-ltd
     * @apiEndpointURIFragment string pubKey FEDCBA
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/companies/credentials/deleteOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Credentials::deleteOne
     */
    private static function deleteOne(App $app, callable $auth, callable $permission) {
        $app
            ->delete(
                '/companies/{companySlug:[a-zA-Z0-9_-]+}/credentials/{pubKey:[a-zA-Z0-9]+}',
                'App\Controller\Credentials:deleteOne'
            )
            ->add($permission(Permission::PRIVATE_ACTION))
            ->add($auth(Auth::COMP_PRIVKEY))
            ->setName('credentials:deleteOne');
    }
}
