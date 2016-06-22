<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Route;

use App\Middleware\Auth;
use Interop\Container\ContainerInterface;
use Slim\App;

/**
 * Credentials routing definitions.
 *
 * @link docs/companies/credentials/overview.md
 *
 * @see App\Controller\Companies
 */
class Credentials implements RouteInterface {
    /**
     * {@inheritDoc}
     */
    public static function getPublicNames() {
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
     * {@inheritDoc}
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

        $container      = $app->getContainer();
        $authMiddleware = $container->get('authMiddleware');

        self::listAll($app, $authMiddleware);
        self::createNew($app, $authMiddleware);
        self::deleteAll($app, $authMiddleware);
        self::getOne($app, $authMiddleware);
        self::updateOne($app, $authMiddleware);
        self::deleteOne($app, $authMiddleware);
    }

    /**
     * List all Credentials
     *
     * Retrieve a complete list of all credentials that belong to the requesting company.
     *
     * @apiEndpoint GET /companies/:companySlug/credentials
     * @apiAuth header key compPrivKey Company's Private Key
     * @apiAuth query key compPrivKey Company's Private Key
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/companies/credentials/listAll.md
     *
     * @uses App\Middleware\Auth::__invoke
     * @see App\Controller\Credentials::listAll
     */
    private static function listAll(App $app, callable $auth) {
        $app
            ->get(
                '/companies/{companySlug:[a-zA-Z0-9_-]+}/credentials',
                'App\Controller\Credentials:listAll'
            )
            ->add($auth(Auth::COMP_PRIVKEY))
            ->setName('credentials:listAll');
    }

    /**
     * Create new Credential
     *
     * Create a new credential for the requesting company.
     *
     * @apiEndpoint POST /companies/:companySlug/credentials
     * @apiAuth header key compPrivKey Company's Private Key
     * @apiAuth query key compPrivKey Company's Private Key
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/companies/credentials/createNew.md
     *
     * @uses App\Middleware\Auth::__invoke
     * @see App\Controller\Credentials::createNew
     */
    private static function createNew(App $app, callable $auth) {
        $app
            ->post(
                '/companies/{companySlug:[a-zA-Z0-9_-]+}/credentials',
                'App\Controller\Credentials:createNew'
            )
            ->add($auth(Auth::COMP_PRIVKEY))
            ->setName('credentials:createNew');
    }

    /**
     * Delete All Credentials
     *
     * Delete all credentials that belong to the requesting company.
     *
     * @apiEndpoint DELETE /companies/:companySlug/credentials
     * @apiAuth header key compPrivKey Company's Private Key
     * @apiAuth query key compPrivKey Company's Private Key
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/companies/credentials/deleteAll.md
     *
     * @uses App\Middleware\Auth::__invoke
     * @see App\Controller\Credentials::deleteAll
     */
    private static function deleteAll(App $app, callable $auth) {
        $app
            ->delete(
                '/companies/{companySlug:[a-zA-Z0-9_-]+}/credentials',
                'App\Controller\Credentials:deleteAll'
            )
            ->add($auth(Auth::COMP_PRIVKEY))
            ->setName('credentials:deleteAll');
    }

    /**
     * Retrieve a single Credential
     *
     * Retrieves all public information from a Credential
     *
     * @apiEndpoint GET /companies/:companySlug/credentials/:pubKey
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/companies/credentials/getOne.md
     *
     * @uses App\Middleware\Auth::__invoke
     * @see App\Controller\Credentials::getOne
     */
    private static function getOne(App $app, callable $auth) {
        $app
            ->get(
                '/companies/{companySlug:[a-zA-Z0-9_-]+}/credentials/{pubKey:[a-zA-Z0-9]+}',
                'App\Controller\Credentials:getOne'
            )
            ->add($auth(Auth::COMP_PRIVKEY))
            ->setName('credentials:getOne');
    }

    /**
     * Update a single Credential
     *
     * Updates Credential's specific information
     *
     * @apiEndpoint POST /companies/:companySlug/credentials/:pubKey
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/companies/credentials/updateOne.md
     *
     * @uses App\Middleware\Auth::__invoke
     * @see App\Controller\Credentials::updateOne
     */
    private static function updateOne(App $app, callable $auth) {
        $app
            ->post(
                '/companies/{companySlug:[a-zA-Z0-9_-]+}/credentials/{pubKey:[a-zA-Z0-9]+}',
                'App\Controller\Credentials:updateOne'
            )
            ->add($auth(Auth::COMP_PRIVKEY))
            ->setName('credentials:updateOne');
    }

    /**
     * Deletes a single Credential
     *
     * Deletes a single Credential that belongs to the requesting company.
     *
     * @apiEndpoint DELETE /companies/:companySlug/credentials/:pubKey
     * @apiAuth header key compPrivKey Company's Private Key
     * @apiAuth query key compPrivKey Company's Private Key
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/companies/credentials/deleteOne.md
     *
     * @uses App\Middleware\Auth::__invoke
     * @see App\Controller\Credentials::deleteOne
     */
    private static function deleteOne(App $app, callable $auth) {
        $app
            ->delete(
                '/companies/{companySlug:[a-zA-Z0-9_-]+}/credentials/{pubKey:[a-zA-Z0-9]+}',
                'App\Controller\Credentials:deleteOne'
            )
            ->add($auth(Auth::COMP_PRIVKEY))
            ->setName('credentials:deleteOne');
    }
}
