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
 * @apiRoute /companies
 */
class Companies implements RouteInterface {
    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
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

        // [GET /1.0/companies](companies/listAll.md)
        /**
         * List all Companies
         *
         * Retrieve a complete list of all child companies that belong to the current requesting company.
         *
         * @apiEndpoint GET /companies
         * @apiAuth CompanyPrivKey Company's Private Key
         *
         * @see App\Controller\Companies::listAll
         */
        $app
            ->get(
                '/companies',
                'App\Controller\Companies:listAll'
            )
            ->add($authMiddleware(Auth::CompanyPrivKey))
            ->setName('companies:listAll');

        // [POST /1.0/companies](companies/createNew.md)
        /**
         * Create new Company
         *
         * Create a new child company for the current requesting company.
         *
         * @apiEndpoint POST /companies
         * @apiAuth CompanyPrivKey Company's Private Key
         *
         * @see App\Controller\Companies::createNew
         */
        $app
            ->post(
                '/companies',
                'App\Controller\Companies:createNew'
            )
            ->add($authMiddleware(Auth::CompanyPrivKey))
            ->setName('companies:createNew');

        // [DELETE /1.0/companies](companies/deleteAll.md)
        /**
         * Delete all Companies
         *
         * Delete all child companies that belong to the current requesting company.
         *
         * @apiEndpoint DELETE /companies
         * @apiAuth CompanyPrivKey Company's Private Key
         *
         * @see App\Controller\Companies::deleteAll
         */
        $app
            ->delete(
                '/companies',
                'App\Controller\Companies:deleteAll'
            )
            ->add($authMiddleware(Auth::CompanyPrivKey))
            ->setName('companies:deleteAll');

        // [GET /1.0/companies/:companySlug](companies/getCompany.md)
        /**
         * Retrieve a single Company
         *
         * Retrieves all public information from a Company
         *
         * @apiEndpoint GET /companies/:companySlug
         *
         * @see App\Controller\Companies::getOne
         */
        $app
            ->get(
                '/companies/{companySlug:[a-zA-Z0-9_-]+}',
                'App\Controller\Companies:getOne'
            )
            ->add($authMiddleware(Auth::None))
            ->setName('companies:getOne');

        // [POST /1.0/companies/:companySlug](companies/updateCompany.md)
        /**
         * Update a single Company
         *
         * Updates Company's specific information
         *
         * @apiEndpoint POST /companies/:companySlug
         * @apiAuth CompanyPrivKey Company's Private Key
         *
         * @see App\Controller\Companies::updateOne
         */
        $app
            ->post(
                '/companies/{companySlug:[a-zA-Z0-9_-]+}',
                'App\Controller\Companies:updateOne'
            )
            ->add($authMiddleware(Auth::CompanyPrivKey))
            ->setName('companies:updateOne');

        // [DELETE /1.0/companies/:companySlug](companies/deleteCompany.md)
        /**
         * Deletes a single Company
         *
         * Deletes the current requesting company or a child company that belongs to it.
         *
         * @apiEndpoint DELETE /companies/:companySlug
         * @apiAuth CompanyPrivKey Company's Private Key
         *
         * @see App\Controller\Companies::deleteOne
         */
        $app
            ->delete(
                '/companies/{companySlug:[a-zA-Z0-9_-]+}',
                'App\Controller\Companies:deleteOne'
            )
            ->add($authMiddleware(Auth::CompanyPrivKey))
            ->setName('companies:deleteOne');
    }
}
