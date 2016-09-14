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
 * Company Members
 *
 * A Company Member is a user profile for an employee of a Company with an adjustable level of permissions and access to specific information. (eg. for distinguishing a low level employee with read-only permissions from an administrator)
 *
<<<<<<< HEAD
 * @link docs/management/members/overview.md
 * @see App\Controller\Members
=======
 * @link docs/companies/members/overview.md
 * @see App\Controller\Companies
>>>>>>> 38414c0f682f504064149c6715641486b5378a8f
 */
class Members implements RouteInterface {
    /**
     * {@inheritdoc}
     */
    public static function getPublicNames() : array {
        return [
            'members:listAll',
            'members:createNew',
            'members:getOne',
            'members:updateOne',
            'members:deleteOne'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function register(App $app) {
        $app->getContainer()[\App\Controller\Members::class] = function (ContainerInterface $container) {
            return new \App\Controller\Members(
                $container->get('repositoryFactory')->create('Member'),
                $container->get('repositoryFactory')->create('User'),
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
        self::getOne($app, $authMiddleware, $permissionMiddleware);
        self::updateOne($app, $authMiddleware, $permissionMiddleware);
        self::deleteOne($app, $authMiddleware, $permissionMiddleware);
    }

    /**
     * List all Members.
     *
     * Retrieves a complete list of all members that belong to the requesting company.
     *
     * @apiEndpoint GET /companies/members
     * @apiGroup Company Members
<<<<<<< HEAD
     * @apiAuth header token IdentityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     * @apiAuth query token identityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
=======
     * @apiAuth header token IdentityToken XXX A valid Identity Token
     * @apiAuth query token IdentityToken XXX A valid Identity Token
>>>>>>> 38414c0f682f504064149c6715641486b5378a8f
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/companies/members/listAll.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Members::listAll
     */
    private static function listAll(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/companies/{companySlug:[a-z0-9_-]+}/members',
                'App\Controller\Members:listAll'
            )
            ->add($permission(EndpointPermission::PRIVATE_ACTION))
            ->add($auth(Auth::IDENTITY))
            ->setName('members:listAll');
    }

    /**
     * Create new Member.
     *
     * Creates a new member for the requesting company.
     *
     * @apiEndpoint POST /companies/members
     * @apiGroup Company Members
<<<<<<< HEAD
     * @apiAuth header token IdentityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     * @apiAuth query token identityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
=======
     * @apiAuth header token IdentityToken XXX A valid Identity Token
     * @apiAuth query token IdentityToken XXX A valid Identity Token
>>>>>>> 38414c0f682f504064149c6715641486b5378a8f
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/companies/members/createNew.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Members::createNew
     */
    private static function createNew(App $app, callable $auth, callable $permission) {
        $app
            ->post(
                '/companies/{companySlug:[a-z0-9_-]+}/members',
                'App\Controller\Members:createNew'
            )
            ->add($permission(EndpointPermission::PRIVATE_ACTION))
            ->add($auth(Auth::IDENTITY))
            ->setName('members:createNew');
    }

    /**
     * Delete All Members.
     *
     * Deletes all members that belong to the requesting company.
     *
<<<<<<< HEAD
     * @apiEndpoint DELETE /management/members
     * @apiGroup Company Members
     * @apiAuth header token IdentityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     * @apiAuth query token identityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
=======
     * @apiEndpoint PUT /companies/members/{userName}
     * @apiGroup Company Members
     * @apiAuth header token IdentityToken XXX A valid Identity Token
     * @apiAuth query token IdentityToken XXX A valid Identity Token
     * @apiEndpointURIFragment string userName johndoe
>>>>>>> 38414c0f682f504064149c6715641486b5378a8f
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
<<<<<<< HEAD
     * @link docs/management/members/deleteAll.md
=======
     * @link docs/companies/members/updateOne.md
>>>>>>> 38414c0f682f504064149c6715641486b5378a8f
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Members::deleteAll
     */
    private static function deleteAll(App $app, callable $auth, callable $permission) {
        $app
<<<<<<< HEAD
            ->delete(
                '/management/members',
                'App\Controller\Members:deleteAll'
            )
            ->add($permission(EndpointPermission::PRIVATE_ACTION))
            ->add($auth(Auth::COMPANY))
            ->setName('members:deleteAll');
=======
            ->put(
                '/companies/{companySlug:[a-z0-9_-]+}/members/{memberId:[0-9]+}',
                'App\Controller\Members:updateOne'
            )
            ->add($permission(EndpointPermission::PRIVATE_ACTION))
            ->add($auth(Auth::IDENTITY))
            ->setName('members:updateOne');
>>>>>>> 38414c0f682f504064149c6715641486b5378a8f
    }

    /*
     * Retrieve a single Member.
<<<<<<< HEAD
     *
     * Retrieves all public information from a Member
     *
     * @apiEndpoint GET /management/members/{memberId}
     * @apiGroup Company Members
     * @apiAuth header token IdentityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     * @apiAuth query token identityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     * @apiEndpointURIFragment int encodedMemberId 1321189817
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/management/members/getOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Members::getOne
     */
    private static function getOne(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/management/members/{memberId:[0-9]+}',
                'App\Controller\Members:getOne'
            )
            ->add($permission(EndpointPermission::PRIVATE_ACTION))
            ->add($auth(Auth::COMPANY))
            ->setName('members:getOne');
    }


    /**
     * Update a single Member.
=======
>>>>>>> 38414c0f682f504064149c6715641486b5378a8f
     *
     * Updates the role for a single Member.
     *
<<<<<<< HEAD
     * @apiEndpoint PUT /management/members/{memberId}
     * @apiGroup Company Members
     * @apiAuth header token IdentityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     * @apiAuth query token identityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     * @apiEndpointURIFragment int encodedMemberId 1321189817
=======
     * @apiEndpoint GET /companies/members/{userName}
     * @apiGroup Company Members
     * @apiAuth header token IdentityToken XXX A valid Identity Token
     * @apiAuth query token IdentityToken XXX A valid Identity Token
     * @apiEndpointURIFragment string userName
>>>>>>> 38414c0f682f504064149c6715641486b5378a8f
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
<<<<<<< HEAD
     * @link docs/management/members/updateOne.md
=======
     * @link docs/companies/members/getOne.md
>>>>>>> 38414c0f682f504064149c6715641486b5378a8f
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Members::updateOne
     */
    private static function updateOne(App $app, callable $auth, callable $permission) {
        $app
<<<<<<< HEAD
            ->put(
                '/management/members/{memberId:[0-9]+}',
                'App\Controller\Members:updateOne'
            )
            ->add($permission(EndpointPermission::PRIVATE_ACTION))
            ->add($auth(Auth::COMPANY))
            ->setName('members:updateOne');
=======
            ->get(
                '/companies/{companySlug:[a-z0-9_-]+}/members/{memberId:[0-9]+}',
                'App\Controller\Members:getOne'
            )
            ->add($permission(EndpointPermission::PRIVATE_ACTION))
            ->add($auth(Auth::IDENTITY))
            ->setName('members:getOne');
>>>>>>> 38414c0f682f504064149c6715641486b5378a8f
    }

    /*
     * Deletes a single Member.
     *
     * Deletes a single Member that belongs to the requesting company.
     *
<<<<<<< HEAD
     * @apiEndpoint DELETE /management/members/{memberId}
     * @apiGroup Company Members
     * @apiAuth header token IdentityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     * @apiAuth query token identityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     * @apiEndpointURIFragment int encodedMemberId 1321189817
=======
     * @apiEndpoint DELETE /companies/members/{userName}
     * @apiGroup Company Members
     * @apiAuth header token IdentityToken XXX A valid Identity Token
     * @apiAuth query token IdentityToken XXX A valid Identity Token
     * @apiEndpointURIFragment string userName
>>>>>>> 38414c0f682f504064149c6715641486b5378a8f
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/companies/members/deleteOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Members::deleteOne
     */
    private static function deleteOne(App $app, callable $auth, callable $permission) {
        $app
            ->delete(
                '/companies/{companySlug:[a-z0-9_-]+}/members/{memberId:[0-9]+}',
                'App\Controller\Members:deleteOne'
            )
            ->add($permission(EndpointPermission::PRIVATE_ACTION))
            ->add($auth(Auth::IDENTITY))
            ->setName('members:deleteOne');
    }
}
