<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Route\Company;

use App\Entity\Role;
use App\Middleware\Auth;
use App\Middleware\EndpointPermission;
use App\Route\RouteInterface;
use Interop\Container\ContainerInterface;
use Slim\App;

/**
 * Company Members.
 *
 * A Company Member is a user profile for an employee of a Company with an adjustable level of permissions and access to specific information. (eg. for distinguishing a low level employee with read-only permissions from an administrator)
 *
 * @link docs/companies/members/overview.md
 * @see App\Controller\Companies
 */
class Members implements RouteInterface {
    /**
     * {@inheritdoc}
     */
    public static function getPublicNames() : array {
        return [
            'members:listAll',
            'members:createNewInvitation',
            'members:getOne',
            'members:updateOne',
            'members:deleteOne',
            'members:deleteInvitation'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function register(App $app) {
        $app->getContainer()[\App\Controller\Company\Members::class] = function (ContainerInterface $container) {
            return new \App\Controller\Company\Members(
                $container->get('repositoryFactory')->create('Company\Member'),
                $container->get('repositoryFactory')->create('Company\Invitation'),
                $container->get('repositoryFactory')->create('User'),
                $container->get('commandBus'),
                $container->get('commandFactory')
            );
        };

        $container            = $app->getContainer();
        $authMiddleware       = $container->get('authMiddleware');
        $permissionMiddleware = $container->get('endpointPermissionMiddleware');

        self::listAll($app, $authMiddleware, $permissionMiddleware);
        self::getMembership($app, $authMiddleware, $permissionMiddleware);
        self::getOne($app, $authMiddleware, $permissionMiddleware);
        self::updateOne($app, $authMiddleware, $permissionMiddleware);
        self::deleteOne($app, $authMiddleware, $permissionMiddleware);
    }

    /**
     * List all Members.
     *
     * Retrieves a complete list of all members that belong to the requesting company.
     *
     * @apiEndpoint GET /companies/{companySlug}/members
     * @apiGroup Company Members
     * @apiAuth header token IdentityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     * @apiAuth query token IdentityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     * @apiEndpointURIFragment string companySlug veridu-ltd
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
     * @see App\Controller\Company\Members::listAll
     */
    private static function listAll(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/companies/{companySlug:[a-z0-9_-]+}/members',
                'App\Controller\Company\Members:listAll'
            )
            ->add($permission(
                EndpointPermission::PRIVATE_ACTION,
                Role::COMPANY_OWNER_BIT | Role::COMPANY_ADMIN_BIT
            ))
            ->add($auth(Auth::IDENTITY))
            ->setName('members:listAll');
    }

    /**
     * Get the membership related to the requesting Identity.
     *
     * Retrieve a member entity related to that company and identity.
     *
     * @apiEndpoint GET /companies/{companySlug}/membership
     * @apiGroup Company Members
     * @apiAuth header token IdentityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     * @apiAuth query token IdentityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     * @apiEndpointURIFragment string companySlug veridu-ltd
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/companies/members/getMembership.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Company\Members::getMembership
     */
    private static function getMembership(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/companies/{companySlug:[a-z0-9_-]+}/membership',
                'App\Controller\Company\Members:getMembership'
            )
            ->add($permission(
                EndpointPermission::PRIVATE_ACTION,
                Role::COMPANY_OWNER_BIT | Role::COMPANY_ADMIN_BIT | Role::COMPANY_REVIEWER_BIT
            ))
            ->add($auth(Auth::IDENTITY))
            ->setName('members:getMembership');
    }

    /**
     * Retrieve a single Member.
     *
     * Retrieves all public information from a Member
     *
     * @apiEndpoint GET /companies/{companySlug}/members/{memberId}
     * @apiGroup Company Members
     * @apiAuth header token IdentityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     * @apiAuth query token IdentityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     * @apiEndpointURIFragment string companySlug veridu-ltd
     * @apiEndpointURIFragment int memberId 1243
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/companies/members/getOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Company\Members::getOne
     */
    private static function getOne(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/companies/{companySlug:[a-z0-9_-]+}/members/{memberId:[0-9]+}',
                'App\Controller\Company\Members:getOne'
            )
            ->add($permission(
                EndpointPermission::PRIVATE_ACTION,
                Role::COMPANY_OWNER_BIT | Role::COMPANY_ADMIN_BIT
            ))
            ->add($auth(Auth::IDENTITY))
            ->setName('members:getOne');
    }

    /**
     * Updates a single Member.
     *
     * Updates one member data.
     *
     * @apiEndpoint PUT /companies/{companySlug}/members/{memberId}
     * @apiGroup Company Members
     * @apiAuth header token IdentityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     * @apiAuth query token IdentityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     * @apiEndpointURIFragment string companySlug veridu-ltd
     * @apiEndpointURIFragment int memberId 1243
     *
     * @param \Slim\App $app
     * @param \callable $auth
     * @param \callable $permission
     *
     * @return void
     *
     * @link docs/companies/members/updateOne.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\Permission::__invoke
     * @see App\Controller\Company\Members::updateOne
     */
    private static function updateOne(App $app, callable $auth, callable $permission) {
        $app
            ->put(
                '/companies/{companySlug:[a-z0-9_-]+}/members/{memberId:[0-9]+}',
                'App\Controller\Company\Members:updateOne'
            )
            ->add($permission(
                EndpointPermission::PRIVATE_ACTION,
                Role::COMPANY_OWNER_BIT | Role::COMPANY_ADMIN_BIT
            ))
            ->add($auth(Auth::IDENTITY))
            ->setName('members:updateOne');
    }

    /**
     * Deletes a single Member.
     *
     * Deletes one member from the database.
     *
     * @apiEndpoint DELETE /companies/{companySlug}/members/{memberId}
     * @apiGroup Company Members
     * @apiAuth header token IdentityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     * @apiAuth query token IdentityToken wqxehuwqwsthwosjbxwwsqwsdi A valid Identity Token
     * @apiEndpointURIFragment string companySlug veridu-ltd
     * @apiEndpointURIFragment int memberId 1243
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
     * @see App\Controller\Company\Members::deleteOne
     */
    private static function deleteOne(App $app, callable $auth, callable $permission) {
        $app
            ->delete(
                '/companies/{companySlug:[a-z0-9_-]+}/members/{memberId:[0-9]+}',
                'App\Controller\Company\Members:deleteOne'
            )
            ->add($permission(
                EndpointPermission::PRIVATE_ACTION,
                Role::COMPANY_OWNER_BIT | Role::COMPANY_ADMIN_BIT
            ))
            ->add($auth(Auth::IDENTITY))
            ->setName('members:deleteOne');
    }
}
