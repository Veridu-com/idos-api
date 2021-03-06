<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Route\Profile;

use App\Controller\ControllerInterface;
use App\Middleware\Auth;
use App\Middleware\EndpointPermission;
use App\Route\RouteInterface;
use Interop\Container\ContainerInterface;
use Slim\App;

/**
 * Attribute Score.
 *
 * An Attribute Score is a numerical score given to a specific Attribute representing how confident the API is in
 * this information. If a User has multiple Attribute Candidates across multiple sources, then the API will provide
 * the Attribute with a score representing how likely of it being true.
 *
 * @link docs/profiles/attributes/score/overview.md
 * @see \App\Controller\Profile\Scores
 */
class Scores implements RouteInterface {
    /**
     * {@inheritdoc}
     */
    public static function getPublicNames() : array {
        return [
            'score:listAll',
            'score:getOne',
            'score:createNew',
            'score:updateOne',
            'score:upsertOne',
            'score:deleteOne',
            'score:deleteAll'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function register(App $app) : void {
        $app->getContainer()[\App\Controller\Profile\Scores::class] = function (ContainerInterface $container) : ControllerInterface {
            $repositoryFactory = $container->get('repositoryFactory');

            return new \App\Controller\Profile\Scores(
                $repositoryFactory
                    ->create('Profile\Score'),
                $repositoryFactory
                    ->create('Profile\Attribute'),
                $container
                    ->get('commandBus'),
                $container
                    ->get('commandFactory')
            );
        };

        $container            = $app->getContainer();
        $authMiddleware       = $container->get('authMiddleware');
        $permissionMiddleware = $container->get('endpointPermissionMiddleware');

        self::listAll($app, $authMiddleware, $permissionMiddleware);
        self::getOne($app, $authMiddleware, $permissionMiddleware);
        self::createNew($app, $authMiddleware, $permissionMiddleware);
        self::updateOne($app, $authMiddleware, $permissionMiddleware);
        self::upsertOne($app, $authMiddleware, $permissionMiddleware);
        self::deleteOne($app, $authMiddleware, $permissionMiddleware);
        self::deleteAll($app, $authMiddleware, $permissionMiddleware);
    }

    /**
     * List all scores.
     *
     * Retrieve a complete list of the scores from a given attribute.
     *
     * @apiEndpoint GET /profiles/{userName}/scores
     * @apiGroup Profile
     * @apiAuth header token CredentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential Token
     * @apiAuth query token credentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential Token
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     *
     * @param \Slim\App $app
     * @param callable  $auth
     * @param callable  $permission
     *
     * @return void
     *
     * @link docs/attributes/score/listAll.md
     * @see \App\Middleware\Auth::__invoke
     * @see \App\Middleware\Permission::__invoke
     * @see \App\Controller\Profile\Scores::listAll
     */
    private static function listAll(App $app, callable $auth, callable $permission) : void {
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
     * Retrieves a score.
     *
     * Retrieves a score from a given attribute.
     *
     * @apiEndpoint GET /profiles/{userName}/scores/{scoreName}
     * @apiGroup Profile
     * @apiAuth header token CredentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential Token
     * @apiAuth query token credentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential Token
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     * @apiEndpointURIFragment string scoreName overall
     *
     * @param \Slim\App $app
     * @param callable  $auth
     * @param callable  $permission
     *
     * @return void
     *
     * @link docs/attributes/score/getOne.md
     * @see \App\Middleware\Auth::__invoke
     * @see \App\Middleware\Permission::__invoke
     * @see \App\Controller\Profile\Scores::getOne
     */
    private static function getOne(App $app, callable $auth, callable $permission) : void {
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
     * Creates a new score.
     *
     * Creates a new score for the given attribute.
     *
     * @apiEndpoint POST /profiles/{userName}/scores
     * @apiGroup Profile
     * @apiAuth header token CredentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential Token
     * @apiAuth query token credentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential Token
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     *
     * @param \Slim\App $app
     * @param callable  $auth
     * @param callable  $permission
     *
     * @return void
     *
     * @link docs/attributes/score/createNew.md
     * @see \App\Middleware\Auth::__invoke
     * @see \App\Middleware\Permission::__invoke
     * @see \App\Controller\Profile\Scores::createNew
     */
    private static function createNew(App $app, callable $auth, callable $permission) : void {
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
     * @apiEndpoint PATCH /profiles/{userName}/scores/{scoreName}
     * @apiGroup Profile
     * @apiAuth header token CredentialToken wqxehuwqwsthwosjbxwwsqwsdi A Valid Credential Token
     * @apiAuth query token credentialToken wqxehuwqwsthwosjbxwwsqwsdi A Valid Credential Token
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     * @apiEndpointURIFragment string scoreName overall
     *
     * @param \Slim\App $app
     * @param callable  $auth
     * @param callable  $permission
     *
     * @return void
     *
     * @link docs/attributes/score/updateOne.md
     * @see \App\Middleware\Auth::__invoke
     * @see \App\Middleware\Permission::__invoke
     * @see \App\Controller\Profile\Scores::updateOne
     */
    private static function updateOne(App $app, callable $auth, callable $permission) : void {
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
     * Create or update a score.
     *
     * Creates or updates a score for the given attribute.
     *
     * @apiEndpoint PUT /profiles/{userName}/scores
     * @apiGroup Profile
     * @apiAuth header token CredentialToken wqxehuwqwsthwosjbxwwsqwsdi A Valid Credential Token
     * @apiAuth query token credentialToken wqxehuwqwsthwosjbxwwsqwsdi A Valid Credential Token
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     *
     * @param \Slim\App $app
     * @param callable  $auth
     * @param callable  $permission
     *
     * @return void
     *
     * @link docs/attributes/score/upsert.md
     * @see \App\Middleware\Auth::__invoke
     * @see \App\Middleware\Permission::__invoke
     * @see \App\Controller\Profile\Scores::upsert
     */
    private static function upsertOne(App $app, callable $auth, callable $permission) : void {
        $app
            ->put(
                '/profiles/{userName:[a-zA-Z0-9_-]+}/scores',
                'App\Controller\Profile\Scores:upsertOne'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::CREDENTIAL))
            ->setName('score:upsertOne');
    }

    /**
     * Deletes a score.
     *
     * Deletes a score from a given attribute.
     *
     * @apiEndpoint DELETE /profiles/{userName}/scores/{scoreName}
     * @apiGroup Profile
     * @apiAuth header token CredentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential Token
     * @apiAuth query token credentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential Token
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     * @apiEndpointURIFragment string scoreName overall
     *
     * @param \Slim\App $app
     * @param callable  $auth
     * @param callable  $permission
     *
     * @return void
     *
     * @link docs/attributes/score/deleteOne.md
     * @see \App\Middleware\Auth::__invoke
     * @see \App\Middleware\Permission::__invoke
     * @see \App\Controller\Profile\Scores::deleteOne
     */
    private static function deleteOne(App $app, callable $auth, callable $permission) : void {
        $app
            ->delete(
                '/profiles/{userName:[a-zA-Z0-9_-]+}/scores/{scoreName:[a-zA-Z0-9_-]+}',
                'App\Controller\Profile\Scores:deleteOne'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::CREDENTIAL))
            ->setName('score:deleteOne');
    }

    /**
     * Deletes all scores.
     *
     * Deletes all scores of a given attribute.
     *
     * @apiEndpoint DELETE /profiles/{userName}/scores
     * @apiGroup Profile
     * @apiAuth header token CredentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential Token
     * @apiAuth query token credentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential Token
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     *
     * @param \Slim\App $app
     * @param callable  $auth
     * @param callable  $permission
     *
     * @return void
     *
     * @link docs/attributes/score/deleteAll.md
     * @see \App\Middleware\Auth::__invoke
     * @see \App\Middleware\Permission::__invoke
     * @see \App\Controller\Profile\Scores::deleteAll
     */
    private static function deleteAll(App $app, callable $auth, callable $permission) : void {
        $app
            ->delete(
                '/profiles/{userName:[a-zA-Z0-9_-]+}/scores',
                'App\Controller\Profile\Scores:deleteAll'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::CREDENTIAL))
            ->setName('score:deleteAll');
    }
}
