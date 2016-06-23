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
 * Companies routing definitions.
 *
 * @link docs/companies/overview.md
 * @see App\Controller\Companies
 */
class Companies implements RouteInterface {
    /**
     * {@inheritdoc}
     */
    public static function getPublicNames() {
        return [
            'companies:listAll',
            'companies:createNew',
            'companies:deleteAll',
            'companies:getOne',
            'companies:updateOne',
            'companies:deleteOne'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function register(App $app) {
        $app->getContainer()[\App\Controller\Companies::class] = function (ContainerInterface $container) {
            return new \App\Controller\Companies(
                $container->get('repositoryFactory')->create('Company'),
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
     * List all Companies.
     *
     * Retrieve a complete list of all child companies that belong to the requesting company.
     *
     * @apiEndpoint GET /companies
     * @apiAuth header key compPrivKey Company's Private Key
     * @apiAuth query key compPrivKey Company's Private Key
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/companies/listAll.md
     *
     * @uses App\Middleware\Auth::__invoke
     *
     * @see App\Controller\Companies::listAll
     */
    private static function listAll(App $app, callable $auth) {
        $app
            ->get(
                '/companies',
                'App\Controller\Companies:listAll'
            )
            ->add($auth(Auth::COMP_PRIVKEY))
            ->setName('companies:listAll');
    }

    /**
     * Create new Company.
     *
     * Create a new child company for the requesting company.
     *
     * @apiEndpoint POST /companies
     * @apiAuth header key compPrivKey Company's Private Key
     * @apiAuth query key compPrivKey Company's Private Key
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/companies/createNew.md
     *
     * @uses App\Middleware\Auth::__invoke
     *
     * @see App\Controller\Companies::createNew
     */
    private static function createNew(App $app, callable $auth) {
        $app
            ->post(
                '/companies',
                'App\Controller\Companies:createNew'
            )
            ->add($auth(Auth::COMP_PRIVKEY))
            ->setName('companies:createNew');
    }

    /**
     * Delete all Companies.
     *
     * Delete all child companies that belong to the requesting company.
     *
     * @apiEndpoint DELETE /companies
     * @apiAuth header key compPrivKey Company's Private Key
     * @apiAuth query key compPrivKey Company's Private Key
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/companies/deleteAll.md
     *
     * @uses App\Middleware\Auth::__invoke
     *
     * @see App\Controller\Companies::deleteAll
     */
    private static function deleteAll(App $app, callable $auth) {
        $app
            ->delete(
                '/companies',
                'App\Controller\Companies:deleteAll'
            )
            ->add($auth(Auth::COMP_PRIVKEY))
            ->setName('companies:deleteAll');
    }

    /**
     * Retrieve a single Company.
     *
     * Retrieves all public information from a Company
     *
     * @apiEndpoint GET /companies/{companySlug}
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/companies/getOne.md
     * @see App\Controller\Companies::getOne
     */
    private static function getOne(App $app, callable $auth) {
        $app
            ->get(
                '/companies/{companySlug:[a-zA-Z0-9_-]+}',
                'App\Controller\Companies:getOne'
            )
            ->add($auth(Auth::NONE))
            ->setName('companies:getOne');
    }

    /**
     * Update a single Company.
     *
     * Updates Company's specific information
     *
     * @apiEndpoint POST /companies/:companySlug
     * @apiAuth header key compPrivKey Company's Private Key
     * @apiAuth query key compPrivKey Company's Private Key
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/companies/updateOne.md
     *
     * @uses App\Middleware\Auth::__invoke
     *
     * @see App\Controller\Companies::updateOne
     */
    private static function updateOne(App $app, callable $auth) {
        $app
            ->post(
                '/companies/{companySlug:[a-zA-Z0-9_-]+}',
                'App\Controller\Companies:updateOne'
            )
            ->add($auth(Auth::COMP_PRIVKEY))
            ->setName('companies:updateOne');
    }

    /**
     * Deletes a single Company.
     *
     * Deletes the requesting company or a child company that belongs to it.
     *
     * @apiEndpoint DELETE /companies/:companySlug
     * @apiAuth header key compPrivKey Company's Private Key
     * @apiAuth query key compPrivKey Company's Private Key
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/companies/deleteOne.md
     *
     * @uses App\Middleware\Auth::__invoke
     *
     * @see App\Controller\Companies::deleteOne
     */
    private static function deleteOne(App $app, callable $auth) {
        $app
            ->delete(
                '/companies/{companySlug:[a-zA-Z0-9_-]+}',
                'App\Controller\Companies:deleteOne'
            )
            ->add($auth(Auth::COMP_PRIVKEY))
            ->setName('companies:deleteOne');
    }
}
