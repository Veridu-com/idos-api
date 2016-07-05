<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Route;

use App\Middleware\Auth;
use Interop\Container\ContainerInterface;
use Slim\App;

/**
 * Permissions routing definitions.
 *
 * @link docs/companies/permissions/overview.md
 * @see App\Controller\Permissions
 */
class Permissions implements RouteInterface {
    /**
     * {@inheritdoc}
     */
    public static function getPublicNames() {
        return [
            'permissions:listAll',
            'permissions:listAllFromSection',
            'permissions:deleteAll',
            'permissions:createNew',
            'permissions:getOne',
            'permissions:updateOne',
            'permissions:deleteOne'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function register(App $app) {
        $app->getContainer()[\App\Controller\Permissions::class] = function (ContainerInterface $container) {
            return new \App\Controller\Permissions(
                $container->get('repositoryFactory')->create('Permission'),
                $container->get('commandBus'),
                $container->get('commandFactory'),
                $container->get('optimus')
            );
        };

        $container      = $app->getContainer();
        $authMiddleware = $container->get('authMiddleware');

        self::listAll($app, $authMiddleware);
        self::listAllFromSection($app, $authMiddleware);
        self::deleteAll($app, $authMiddleware);
        self::createNew($app, $authMiddleware);
        self::getOne($app, $authMiddleware);
        self::updateOne($app, $authMiddleware);
        self::deleteOne($app, $authMiddleware);
    }

    /**
     * List all Permissions.
     *
     * Retrieve a complete list of all permissions that belong to the requesting company.
     *
     * @apiEndpoint GET /companies/{companySlug}/permissions
     * @apiAuth header key compPrivKey Company's Private Key
     * @apiAuth query key compPrivKey Company's Private Key
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/companies/permissions/listAll.md
     *
     * @uses App\Middleware\Auth::__invoke
     *
     * @see App\Controller\Permissions::listAll
     */
    private static function listAll(App $app, callable $auth) {
        $app
            ->get(
                '/companies/{companySlug:[a-zA-Z0-9_-]+}/permissions',
                'App\Controller\Permissions:listAll'
            )
            ->add($auth(Auth::COMP_PRIVKEY))
            ->setName('permissions:listAll');
    }
    /**
     * List all Permissions from section.
     *
     * Retrieve a complete list of all permissions that belong to the requesting company and has the given section.
     *
     * @apiEndpoint GET /companies/{companySlug}/permissions/{section}
     * @apiAuth header key compPrivKey Company's Private Key
     * @apiAuth query key compPrivKey Company's Private Key
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/companies/permissions/listAllFromSection.md
     *
     * @uses App\Middleware\Auth::__invoke
     *
     * @see App\Controller\Permissions::listAllFromSection
     */
    private static function listAllFromSection(App $app, callable $auth) {
        $app
            ->get(
                '/companies/{companySlug:[a-zA-Z0-9_-]+}/permissions/{section:[a-zA-Z0-9_-]+}',
                'App\Controller\Permissions:listAllFromSection'
            )
            ->add($auth(Auth::COMP_PRIVKEY))
            ->setName('permissions:listAllFromSection');
    }

    /**
     * Create new Permission.
     *
     * Create a new credential for the requesting company.
     *
     * @apiEndpoint POST /companies/{companySlug}/permissions
     * @apiAuth header key compPrivKey Company's Private Key
     * @apiAuth query key compPrivKey Company's Private Key
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/companies/permissions/createNew.md
     *
     * @uses App\Middleware\Auth::__invoke
     *
     * @see App\Controller\Permissions::createNew
     */
    private static function createNew(App $app, callable $auth) {
        $app
            ->post(
                '/companies/{companySlug:[a-zA-Z0-9_-]+}/permissions',
                'App\Controller\Permissions:createNew'
            )
            ->add($auth(Auth::COMP_PRIVKEY))
            ->setName('permissions:createNew');
    }

    /**
     * Deletes all permissions.
     *
     * Deletes all permissions that belongs to the requesting company.
     *
     * @apiEndpoint DELETE /companies/{companySlug}/permissions
     * @apiAuth header key compPrivKey Company's Private Key
     * @apiAuth query key compPrivKey Company's Private Key
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/companies/permissions/deleteAll.md
     *
     * @uses App\Middleware\Auth::__invoke
     *
     * @see App\Controller\Permissions::deleteAll
     */
    private static function deleteAll(App $app, callable $auth) {
        $app
            ->delete(
                '/companies/{companySlug:[a-zA-Z0-9_-]+}/permissions',
                'App\Controller\Permissions:deleteAll'
            )
            ->add($auth(Auth::COMP_PRIVKEY))
            ->setName('permissions:deleteAll');
    }

    /**
     * Retrieve a single Permission.
     *
     * Retrieves all public information from a Permission.
     *
     * @apiEndpoint GET /companies/{companySlug}/permissions/{section}/{property}
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/companies/permissions/getOne.md
     *
     * @uses App\Middleware\Auth::__invoke
     *
     * @see App\Controller\Permissions::getOne
     */
    private static function getOne(App $app, callable $auth) {
        $app
            ->get(
                '/companies/{companySlug:[a-zA-Z0-9_-]+}/permissions/{section:[a-zA-Z0-9_-]+}/{property:[a-zA-Z0-9_-]+}',
                'App\Controller\Permissions:getOne'
            )
            ->add($auth(Auth::COMP_PRIVKEY))
            ->setName('permissions:getOne');
    }

    /**
     * Update a single Permission.
     *
     * Updates Permission's specific information.
     *
     * @apiEndpoint PUT /companies/{companySlug}/permissions/{section}/{property}
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/companies/permissions/updateOne.md
     *
     * @uses App\Middleware\Auth::__invoke
     *
     * @see App\Controller\Permissions::updateOne
     */
    private static function updateOne(App $app, callable $auth) {
        $app
            ->put(
                '/companies/{companySlug:[a-zA-Z0-9_-]+}/permissions/{section:[a-zA-Z0-9_-]+}/{property:[a-zA-Z0-9_-]+}',
                'App\Controller\Permissions:updateOne'
            )
            ->add($auth(Auth::COMP_PRIVKEY))
            ->setName('permissions:updateOne');
    }

    /**
     * Deletes a single Permission.
     *
     * Deletes a single Permission that belongs to the requesting company.
     *
     * @apiEndpoint DELETE /companies/{companySlug}/permissions/{section}/{property}
     * @apiAuth header key compPrivKey Company's Private Key
     * @apiAuth query key compPrivKey Company's Private Key
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/companies/permissions/deleteOne.md
     *
     * @uses App\Middleware\Auth::__invoke
     *
     * @see App\Controller\Permissions::deleteOne
     */
    private static function deleteOne(App $app, callable $auth) {
        $app
            ->delete(
                '/companies/{companySlug:[a-zA-Z0-9_-]+}/permissions/{section:[a-zA-Z0-9_-]+}/{property:[a-zA-Z0-9_-]+}',
                'App\Controller\Permissions:deleteOne'
            )
            ->add($auth(Auth::COMP_PRIVKEY))
            ->setName('permissions:deleteOne');
    }
}
