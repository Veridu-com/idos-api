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

        // [GET /1.0/companies/:companySlug/credentials](companies/credentials/listAll.md)
        $app
            ->get(
                '/companies/{companySlug:[a-zA-Z0-9_-]+}/credentials',
                'App\Controller\Credentials:listAll'
            )
            ->add($authMiddleware(Auth::CompanyPrivKey))
            ->setName('credentials:listAll');

        // [POST /1.0/companies/:companySlug/credentials](companies/credentials/createNew.md)
        $app
            ->post(
                '/companies/{companySlug:[a-zA-Z0-9_-]+}/credentials',
                'App\Controller\Credentials:createNew'
            )
            ->add($authMiddleware(Auth::CompanyPrivKey))
            ->setName('credentials:createNew');

        // [DELETE /1.0/companies/:companySlug/credentials](companies/credentials/deleteAll.md)
        $app
            ->delete(
                '/companies/{companySlug:[a-zA-Z0-9_-]+}/credentials',
                'App\Controller\Credentials:deleteAll'
            )
            ->add($authMiddleware(Auth::CompanyPrivKey))
            ->setName('credentials:deleteAll');

        // [GET /1.0/companies/:companySlug/credentials/:pubKey](companies/credentials/getCredential.md)
        $app
            ->get(
                '/companies/{companySlug:[a-zA-Z0-9_-]+}/credentials/{pubKey:[a-zA-Z0-9]+}',
                'App\Controller\Credentials:getOne'
            )
            ->add($authMiddleware(Auth::CompanyPrivKey))
            ->setName('credentials:getOne');

        // [POST /1.0/companies/:companySlug/credentials/:pubKey](companies/credentials/updateCredential.md)
        $app
            ->post(
                '/companies/{companySlug:[a-zA-Z0-9_-]+}/credentials/{pubKey:[a-zA-Z0-9]+}',
                'App\Controller\Credentials:updateOne'
            )
            ->add($authMiddleware(Auth::CompanyPrivKey))
            ->setName('credentials:updateOne');

        // [DELETE /1.0/companies/:companySlug/credentials/:pubKey](companies/credentials/deleteCredential.md)
        $app
            ->delete(
                '/companies/{companySlug:[a-zA-Z0-9_-]+}/credentials/{pubKey:[a-zA-Z0-9]+}',
                'App\Controller\Credentials:deleteOne'
            )
            ->add($authMiddleware(Auth::CompanyPrivKey))
            ->setName('credentials:deleteOne');
    }
}
