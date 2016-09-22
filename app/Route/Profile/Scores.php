<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Route\Profile;

use App\Middleware\Auth;
use App\Middleware\EndpointPermission;
use App\Route\RouteInterface;
use Interop\Container\ContainerInterface;
use Slim\App;

/**
 * Score routing definitions.
 *
 * @link docs/profiles/attributes/score/overview.md
 * @see App\Controller\Profile\Scores
 */
class Scores implements RouteInterface {
    /**
     * {@inheritdoc}
     */
    public static function getPublicNames() : array {
        return [
            'score:listAll',
            'score:createNew',
            'score:deleteAll',
            'score:getOne',
            'score:updateOne',
            'score:upsert',
            'score:deleteOne'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function register(App $app) {
        $app->getContainer()[\App\Controller\Profile\Scores::class] = function (ContainerInterface $container) {
            return new \App\Controller\Profile\Scores(
                $container->get('repositoryFactory')->create('Profile\Score'),
                $container->get('repositoryFactory')->create('Profile\Attribute'),
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
        self::upsert($app, $authMiddleware, $permissionMiddleware);
        self::deleteOne($app, $authMiddleware, $permissionMiddleware);
    }

    /**
     * List all scores.
     *
     * Retrieve a complete list of the scores from a given attribute.
     *
     * @apiEndpoint GET /profiles/{userName}/attributes/{attributeName}/scores
     * @apiGroup Attributes Score
     * @apiAuth header token CredentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential Token
     * @apiAuth query token credentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential Token
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     * @apiEndpointURIFragment string attributeName firstName
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/attributes/score/listAll.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Profile\Scores::listAll
     */
    private static function listAll(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/profiles/{userName:[a-zA-Z0-9_-]+}/scores',
                'App\Controller\Profile\Scores:listAll'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::CREDENTIAL))
            ->setName('score:listAll');
    }
    /**
     * Creates a new score.
     *
     * Creates a new score for the given attribute.
     *
     * @apiEndpoint POST /profiles/{userName}/attributes/{attributeName}/scores
     * @apiGroup Attributes Score
     * @apiAuth header token CredentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential Token
     * @apiAuth query token credentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential Token
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     * @apiEndpointURIFragment string attributeName firstName
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/attributes/score/createNew.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Profile\Scores::createNew
     */
    private static function createNew(App $app, callable $auth, callable $permission) {
        $app
            ->post(
                '/profiles/{userName:[a-zA-Z0-9_-]+}/scores',
                'App\Controller\Profile\Scores:createNew'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::CREDENTIAL))
            ->setName('score:createNew');
    }

    /**
     * Update a score.
     *
     * Updates a score for the given attribute.
     *
     * @apiEndpoint PUT /profiles/{userName}/attributes/{attributeName}/scores/{scoreName}
     * @apiGroup Company Members
     * @apiAuth header token CredentialToken wqxehuwqwsthwosjbxwwsqwsdi A Valid Credential Token
     * @apiAuth query token credentialToken wqxehuwqwsthwosjbxwwsqwsdi A Valid Credential Token
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     * @apiEndpointURIFragment string attributeName firstName
     * @apiEndpointURIFragment string scoreName overall
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/attributes/score/updateOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Profile\Scores::updateOne
     */
    private static function updateOne(App $app, callable $auth, callable $permission) {
        $app
            ->patch(
                '/profiles/{userName:[a-zA-Z0-9_-]+}/scores/{scoreName:[a-zA-Z0-9_-]+}',
                'App\Controller\Profile\Scores:updateOne'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::CREDENTIAL))
            ->setName('score:updateOne');
    }

    /**
     * Update a score.
     *
     * Updates a score for the given attribute.
     *
     * @apiEndpoint PUT /profiles/{userName}/attributes/{attributeName}/scores/{scoreName}
     * @apiGroup Company Members
     * @apiAuth header token CredentialToken XXX Company's credential token
     * @apiAuth query token credentialToken XXX Company's credential token
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     * @apiEndpointURIFragment string attributeName firstName
     * @apiEndpointURIFragment string scoreName overall
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/attributes/score/updateOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Profile\Scores::updateOne
     */
    private static function upsert(App $app, callable $auth, callable $permission) {
        $app
            ->put(
                '/profiles/{userName:[a-zA-Z0-9_-]+}/scores',
                'App\Controller\Profile\Scores:upsert'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::CREDENTIAL))
            ->setName('score:upsert');
    }

    /*
     * Retrieves a score.
     *
     * Retrieves a score from a given attribute.
     *
     * @apiEndpoint GET /profiles/{userName}/attributes/{attributeName}/scores/{scoreName}
     * @apiGroup Attributes Score
     * @apiAuth header token CredentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential Token
     * @apiAuth query token credentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential Token
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     * @apiEndpointURIFragment string attributeName firstName
     * @apiEndpointURIFragment string scoreName overall
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/attributes/score/getOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Profile\Scores::getOne
     */
    private static function getOne(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/profiles/{userName:[a-zA-Z0-9_-]+}/scores/{scoreName:[a-zA-Z0-9_-]+}',
                'App\Controller\Profile\Scores:getOne'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::CREDENTIAL))
            ->setName('score:getOne');
    }

    /**
     * Deletes all scores.
     *
     * Deletes all scores of a given attribute.
     *
     * @apiEndpoint DELETE /profiles/{userName}/attributes/{attributeName}/scores
     * @apiGroup Attributes Score
     * @apiAuth header token CredentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential Token
     * @apiAuth query token credentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential Token
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     * @apiEndpointURIFragment string attributeName firstName
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/attributes/score/deleteAll.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Profile\Scores::deleteAll
     */
    private static function deleteAll(App $app, callable $auth, callable $permission) {
        $app
            ->delete(
                '/profiles/{userName:[a-zA-Z0-9_-]+}/scores',
                'App\Controller\Profile\Scores:deleteAll'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::CREDENTIAL))
            ->setName('score:deleteAll');
    }

    /**
     * Deletes a score.
     *
     * Deletes a score from a given attribute.
     *
     * @apiEndpoint DELETE /profiles/{userName}/attributes/{attributeName}/scores/{scoreName}
     * @apiGroup Attributes Score
     * @apiAuth header token CredentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential Token
     * @apiAuth query token credentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential Token
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     * @apiEndpointURIFragment string attributeName firstName
     * @apiEndpointURIFragment string scoreName overall
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/attributes/score/deleteOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Profile\Scores::deleteOne
     */
    private static function deleteOne(App $app, callable $auth, callable $permission) {
        $app
            ->delete(
                '/profiles/{userName:[a-zA-Z0-9_-]+}/scores/{scoreName:[a-zA-Z0-9_-]+}',
                'App\Controller\Profile\Scores:deleteOne'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::CREDENTIAL))
            ->setName('score:deleteOne');
    }
}