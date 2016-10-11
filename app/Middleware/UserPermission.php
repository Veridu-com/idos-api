<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Middleware;

use App\Entity\Role;
use App\Entity\User\RoleAccess;
use App\Exception\NotAllowed;
use App\Exception\NotFound;
use App\Repository\User\RoleAccessInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Route UserPermission Middleware
 * This middleware is responsible for allowing or not access to certain user resources
 * This access control is controlled by the user or company and uses the RoleAccess repository to filter it.
 */
class UserPermission implements MiddlewareInterface {
    /**
     * Default permissions for each role.
     *
     * @var array
     */
    private $defaultPermissions;

    /**
     * Role access repository.
     */
    private $roleAccessRepository;

    /**
     * Gets the access from role.
     *
     * @param int    $identityId The identity identifier
     * @param string $role       The role
     * @param string $resource   The resource
     *
     * @return int The access from role.
     */
    private function getAccessFromRole(int $identityId, string $role, string $resource) : int {
        try {
            $roleAccess = $this->roleAccessRepository->findByIdentityRoleResource($identityId, $role, $resource);
            $access     = $roleAccess->access;
        } catch (NotFound $e) {
            // fallbacks to default permission
            $access = $this->defaultPermissions[$role];
        }

        return $access;
    }

    /**
     * Class constructor.
     *
     * @param \App\Repository\User\RoleAccessInterface $roleAccessRepository The role access repository
     * @param string                                   $resource             The resource
     * @param int                                      $accessLevel          The access level
     *
     * @return void
     */
    public function __construct(RoleAccessInterface $roleAccessRepository, string $resource, int $accessLevel) {
        $this->roleAccessRepository = $roleAccessRepository;
        $this->resource             = $resource;
        $this->accessLevel          = $accessLevel;

        $this->defaultPermissions = [
            Role::COMPANY          => RoleAccess::ACCESS_READ | RoleAccess::ACCESS_WRITE | RoleAccess::ACCESS_EXECUTE,
            Role::COMPANY_ADMIN    => RoleAccess::ACCESS_READ,
            Role::COMPANY_OWNER    => RoleAccess::ACCESS_READ,
            Role::COMPANY_REVIEWER => RoleAccess::ACCESS_READ,
            Role::USER             => RoleAccess::ACCESS_READ,
            Role::GUEST            => RoleAccess::ACCESS_READ
        ];
    }

    /**
     * Invoked function when the middleware is called for that route.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request  The request
     * @param \Psr\Http\Message\ResponseInterface      $response The response
     * @param callable                                 $next     The next callable object
     *
     * @throws \App\Exception\NotAllowed Throws NotAllowed if the actor doesn't have access to the resource
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next) : ResponseInterface {
        $user       = $request->getAttribute('user');
        $targetUser = $request->getAttribute('targetUser');
        $company    = $request->getAttribute('company');

        $routeName = $request->getAttribute('route')->getName();
        $allowed   = false;

        if ($company && $user) {
            throw new \RuntimeException('Invalid Request: user and company cannot be defined simultaneously.');
        }

        if (! $targetUser) {
            $response = $this->allow($response);

            return $next($request, $response);
        }

        // User -> User
        if ($user && $targetUser->id !== $user->id) {
            // @FIXME When company members are developed get back to this middleware and find the specific role for each use case
            $role = Role::USER;

            $access = $this->getAccessFromRole($targetUser->identityId, $role, $this->resource);

            if (($this->accessLevel & $access) !== $this->accessLevel) {
                throw new NotAllowed();
            }
        }

        // use case: Company -> User
        if ($company) {
            $role   = Role::COMPANY;
            $access = $this->getAccessFromRole($targetUser->identityId, $role, $this->resource);
            if (($this->accessLevel & $access) !== $this->accessLevel) {
                throw new NotAllowed();
            }
        }

        $response = $this->allow($response);

        return $next($request, $response);
    }

    private function allow(ResponseInterface $response) : ResponseInterface {
        return $response->withHeader('Allowed', 'true');
    }
}
