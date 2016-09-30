<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Route\Company;

use App\Controller\ControllerInterface;
use App\Middleware\Auth;
use App\Middleware\EndpointPermission;
use App\Route\RouteInterface;
use Interop\Container\ContainerInterface;
use Slim\App;

/**
 * Company Profile.
 *
 * A Company Profile where a Company will access the collection of data on a specific User. This is where the Company can review in detail the raw data, attributes, features, warnings, and score gathered from all sources provided by a specific User
 *
 * @link docs/company/profiles/overview.md
 * @see App\Controller\Company\Profiles
 */
class Profiles implements RouteInterface {
    /**
     * {@inheritdoc}
     */
    public static function getPublicNames() : array {
        return [
            'companyProfiles:listAll',
            'companyProfiles:getOne',
            'companyProfiles:deleteOne',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function register(App $app) {
        $app->getContainer()[\App\Controller\Company\Profiles::class] = function (ContainerInterface $container) : ControllerInterface {
            return new \App\Controller\Company\Profiles(
                $container->get('repositoryFactory')->create('User'),
                $container->get('repositoryFactory')->create('Profile\Review'),
                $container->get('repositoryFactory')->create('Profile\Warning'),
                $container->get('repositoryFactory')->create('Profile\Gate'),
                $container->get('repositoryFactory')->create('Profile\Attribute'),
                $container->get('commandBus'),
                $container->get('commandFactory')
            );
        };

        $container            = $app->getContainer();
        $authMiddleware       = $container->get('authMiddleware');
        $permissionMiddleware = $container->get('endpointPermissionMiddleware');

        self::listAll($app, $authMiddleware, $permissionMiddleware);
        self::getOne($app, $authMiddleware, $permissionMiddleware);
        self::deleteOne($app, $authMiddleware, $permissionMiddleware);
    }

    /**
     * List all CompanyProfiles.
     *
     * Retrieve a complete list of all users that belong to this company.
     *
     * @apiEndpoint GET /companies/{companySlug}/profiles
     * @apiGroup Company
     * @apiAuth header token CompanyToken XXX A valid Company Token
     * @apiAuth query token CompanyToken XXX A valid Company Token
     * @apiEndpointURIFragment string companySlug veridu-ltd
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/companyProfiles/listAll.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Company\Profiles::listAll
     */
    private static function listAll(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/companies/{companySlug:[a-z0-9_-]+}/profiles',
                'App\Controller\Company\Profiles:listAll'
            )
            ->add($permission(EndpointPermission::PRIVATE_ACTION))
            ->add($auth(Auth::IDENTITY))
            ->setName('companyProfiles:listAll');
    }

    /**
     * Retrieve a single company profile.
     *
     * Retrieves all public information from a company profile.
     *
     * @apiEndpoint GET /companies/{companySlug}/profiles/{userId}
     * @apiGroup Company
     * @apiEndpointURIFragment string companySlug veridu-ltd
     * @apiEndpointURIFragment int userId 3215132
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/companyProfiles/getOne.md
     * @see App\Controller\Company\Profiles::getOne
     */
    private static function getOne(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/companies/{companySlug:[a-z0-9_-]+}/profiles/{userId:[0-9_-]+}',
                'App\Controller\Company\Profiles:getOne'
            )
            ->add($permission(EndpointPermission::PRIVATE_ACTION))
            ->add($auth(Auth::IDENTITY))
            ->setName('companyProfiles:getOne');
    }

    /**
     * Deletes a single Company profile.
     *
     * Deletes the requested company profile.
     *
     * @apiEndpoint DELETE /companies/{companySlug}/profiles/{userId}
     * @apiGroup Company
     * @apiAuth header token CompanyToken XXX A valid Company Token
     * @apiAuth query token CompanyToken XXX A valid Company Token
     * @apiEndpointURIFragment string companySlug veridu-ltd
     * @apiEndpointURIFragment int userId 3215132
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/company/profiles/deleteOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Company\Profiles::deleteOne
     */
    private static function deleteOne(App $app, callable $auth, callable $permission) {
        $app
            ->delete(
                '/companies/{companySlug:[a-z0-9_-]+}/profiles/{userId:[0-9_-]+}',
                'App\Controller\Company\Profiles:deleteOne'
            )
            ->add($permission(EndpointPermission::PRIVATE_ACTION))
            ->add($auth(Auth::IDENTITY))
            ->setName('companyProfiles:deleteOne');
    }
}
