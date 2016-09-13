<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Route;

use App\Middleware\Auth;
use App\Middleware\EndpointPermission;
use Interop\Container\ContainerInterface;
use Slim\App;

/**
 * Normalised routing definitions.
 *
 * @link docs/profiles/sources/normalised/overview.md
 * @see App\Controller\Normalised
 */
class Normalised implements RouteInterface {
    /**
     * {@inheritdoc}
     */
    public static function getPublicNames() : array {
        return [
            'normalised:listAll',
            'normalised:createNew',
            'normalised:deleteAll',
            'normalised:getOne',
            'normalised:updateOne',
            'normalised:deleteOne'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function register(App $app) {
        $app->getContainer()[\App\Controller\Normalised::class] = function (ContainerInterface $container) {
            return new \App\Controller\Normalised(
                $container->get('repositoryFactory')->create('Normalised'),
                $container->get('commandBus'),
                $container->get('commandFactory'),
                $container->get('optimus')
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
     * List all normalised source data.
     *
     * Retrieve a complete list of the data normalised by a given source.
     *
     * @apiEndpoint GET /profiles/{userName}/sources/{sourceId:[0-9]+}/normalised
     * @apiGroup Sources Normalised
     * @apiAuth header token CredentialToken XXX A valid Credential Token
     * @apiAuth query token credentialToken XXX A valid Credential Token
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     * @apiEndpointURIFragment int sourceId 12345
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/sources/normalised/listAll.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Normalised::listAll
     */
    private static function listAll(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/profiles/{userName:[a-zA-Z0-9_-]+}/sources/{sourceId:[0-9]+}/normalised',
                'App\Controller\Normalised:listAll'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::CREDENTIAL))
            ->setName('normalised:listAll');
    }

    /**
     * Creates a new normalised source data.
     *
     * Creates a new normalised data for the given source.
     *
     * @apiEndpoint POST /profiles/{userName}/source/{sourceId:[0-9]+}/normalised
     * @apiGroup Sources Normalised
     * @apiAuth header token CredentialToken XXX A valid Credential Token
     * @apiAuth query token credentialToken XXX A valid Credential Token
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     * @apiEndpointURIFragment int sourceId 12345
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/sources/normalised/createNew.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Normalised::createNew
     */
    private static function createNew(App $app, callable $auth, callable $permission) {
        $app
            ->post(
                '/profiles/{userName:[a-zA-Z0-9_-]+}/sources/{sourceId:[0-9]+}/normalised',
                'App\Controller\Normalised:createNew'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::CREDENTIAL))
            ->setName('normalised:createNew');
    }

    /**
     * Update a normalised data.
     *
     * Updates a normalised data in the given source.
     *
     * @apiEndpoint PUT /profiles/{userName}/source/{sourceId:[0-9]+}/normalised/{normalisedName}
     * @apiGroup Company Members
     * @apiAuth header token CredentialToken XXX Company's credential token
     * @apiAuth query token credentialToken XXX Company's credential token
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     * @apiEndpointURIFragment int sourceId 12345
     * @apiEndpointURIFragment string normalisedName data-name
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/management/members/updateOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Members::updateOne
     */
    private static function updateOne(App $app, callable $auth, callable $permission) {
        $app
            ->put(
                '/profiles/{userName:[a-zA-Z0-9_-]+}/sources/{sourceId:[0-9]+}/normalised/{normalisedName:[a-zA-Z0-9]+}',
                'App\Controller\Normalised:updateOne'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::CREDENTIAL))
            ->setName('normalised:updateOne');
    }

    /*
     * Retrieves a normalised data.
     *
     * Retrieves a normalised data from a given source.
     *
     * @apiEndpoint GET /profiles/{userName}/source/{sourceId:[0-9]+}/normalised/{normalisedName}
     * @apiGroup Sources Normalised
     * @apiAuth header token CredentialToken XXX A valid Credential Token
     * @apiAuth query token credentialToken XXX A valid Credential Token
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     * @apiEndpointURIFragment int sourceId 12345
     * @apiEndpointURIFragment string normalisedName data-name
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/sources/normalised/getOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Normalised::getOne
     */
    private static function getOne(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/profiles/{userName:[a-zA-Z0-9_-]+}/sources/{sourceId:[0-9]+}/normalised/{normalisedName:[a-zA-Z0-9]+}',
                'App\Controller\Normalised:getOne'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::CREDENTIAL))
            ->setName('normalised:getOne');
    }

    /**
     * Deletes all normalised data.
     *
     * Deletes all normalised data from the given source.
     *
     * @apiEndpoint DELETE /profiles/{userName}/source/{sourceId:[0-9]+}/normalised
     * @apiGroup Sources Normalised
     * @apiAuth header token CredentialToken XXX A valid Credential Token
     * @apiAuth query token credentialToken XXX A valid Credential Token
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     * @apiEndpointURIFragment int sourceId 12345
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/sources/normalised/deleteAll.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Normalised::deleteAll
     */
    private static function deleteAll(App $app, callable $auth, callable $permission) {
        $app
            ->delete(
                '/profiles/{userName:[a-zA-Z0-9_-]+}/sources/{sourceId:[0-9]+}/normalised',
                'App\Controller\Normalised:deleteAll'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::CREDENTIAL))
            ->setName('normalised:deleteAll');
    }

    /*
     * Deletes a normalised data.
     *
     * Deletes a normalised data from the given source.
     *
     * @apiEndpoint DELETE /profiles/{userName}/source/{sourceId:[0-9]+}/normalised/{normalisedName}
     * @apiGroup Sources Normalised
     * @apiAuth header token CredentialToken XXX A valid Credential Token
     * @apiAuth query token credentialToken XXX A valid Credential Token
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     * @apiEndpointURIFragment int sourceId 12345
     * @apiEndpointURIFragment string normalisedName data-name
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/sources/normalised/deleteOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Normalised::deleteOne
     */
    private static function deleteOne(App $app, callable $auth, callable $permission) {
        $app
            ->delete(
                '/profiles/{userName:[a-zA-Z0-9_-]+}/sources/{sourceId:[0-9]+}/normalised/{normalisedName:[a-zA-Z0-9]+}',
                'App\Controller\Normalised:deleteOne'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::CREDENTIAL))
            ->setName('normalised:deleteOne');
    }
}
