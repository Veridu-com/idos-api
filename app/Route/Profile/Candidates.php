<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
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
 * Profile Candidate.
 *
 * Attribute Candidates are listed as all possible results for an Attribute. If the API extracts multiple results for
 * one Attribute, then it will list each Candidate a numerical support score calculated by the level of presence
 * it has within the Profile. The Candidate with the highest support score will be given as a Profile Attribute.
 *
 * @link docs/profiles/candidate/overview.md
 * @see \App\Controller\Profile\Candidates
 */
class Candidates implements RouteInterface {
    /**
     * {@inheritdoc}
     */
    public static function getPublicNames() : array {
        return [
            'candidate:listAll',
            'candidate:createNew',
            'candidate:deleteAll'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function register(App $app) {
        $app->getContainer()[\App\Controller\Profile\Candidates::class] = function (ContainerInterface $container) : ControllerInterface {
            return new \App\Controller\Profile\Candidates(
                $container->get('repositoryFactory')->create('Profile\Candidate'),
                $container->get('commandBus'),
                $container->get('commandFactory')
            );
        };

        $container            = $app->getContainer();
        $authMiddleware       = $container->get('authMiddleware');
        $permissionMiddleware = $container->get('endpointPermissionMiddleware');

        self::listAll($app, $authMiddleware, $permissionMiddleware);
        self::createNew($app, $authMiddleware, $permissionMiddleware);
        self::deleteAll($app, $authMiddleware, $permissionMiddleware);
    }

    /**
     * List all candidate data.
     *
     * Retrieve a complete list of the candidates by a given user.
     *
     * @apiEndpoint GET /profiles/{userName}/candidates
     * @apiGroup Profile
     * @apiAuth header token CredentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential Token
     * @apiAuth query token credentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential Token
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/sources/candidate/listAll.md
     * @see \App\Middleware\Auth::__invoke
     * @see \App\Middleware\Permission::__invoke
     * @see \App\Controller\Profile\Candidates::listAll
     */
    private static function listAll(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/profiles/{userName:[a-zA-Z0-9_-]+}/candidates',
                'App\Controller\Profile\Candidates:listAll'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::CREDENTIAL))
            ->setName('candidate:listAll');
    }

    /**
     * Creates a new candidate.
     *
     * Creates a new candidate for the given user.
     *
     * @apiEndpoint POST /profiles/{userName}/candidates
     * @apiGroup Profile
     * @apiAuth header token CredentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential Token
     * @apiAuth query token credentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential Token
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/sources/candidate/createNew.md
     * @see \App\Middleware\Auth::__invoke
     * @see \App\Middleware\Permission::__invoke
     * @see \App\Controller\Profile\Candidates::createNew
     */
    private static function createNew(App $app, callable $auth, callable $permission) {
        $app
            ->post(
                '/profiles/{userName:[a-zA-Z0-9_-]+}/candidates',
                'App\Controller\Profile\Candidates:createNew'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::CREDENTIAL))
            ->setName('candidate:createNew');
    }

    /**
     * Deletes all candidates.
     *
     * Deletes all candidates for the given user.
     *
     * @apiEndpoint DELETE /profiles/{userName}/candidates
     * @apiGroup Profile
     * @apiAuth header token CredentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential Token
     * @apiAuth query token credentialToken wqxehuwqwsthwosjbxwwsqwsdi A valid Credential Token
     * @apiEndpointURIFragment string userName 9fd9f63e0d6487537569075da85a0c7f2
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/sources/candidate/deleteAll.md
     * @see \App\Middleware\Auth::__invoke
     * @see \App\Middleware\Permission::__invoke
     * @see \App\Controller\Profile\Candidates::deleteAll
     */
    private static function deleteAll(App $app, callable $auth, callable $permission) {
        $app
            ->delete(
                '/profiles/{userName:[a-zA-Z0-9_-]+}/candidates',
                'App\Controller\Profile\Candidates:deleteAll'
            )
            ->add($permission(EndpointPermission::PUBLIC_ACTION))
            ->add($auth(Auth::CREDENTIAL))
            ->setName('candidate:deleteAll');
    }
}
