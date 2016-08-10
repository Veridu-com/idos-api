<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Middleware;

use App\Entity\Role;
use App\Entity\RoleAccess;
use App\Exception\NotAllowed;
use App\Exception\NotFound;
use App\Repository\RoleAccessInterface;
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
            $roleAccess             = $this->roleAccessRepository->findOne($identityId, $role, $resource);
            $access                 = $roleAccess->access;
        } catch (NotFound $e) {
            // fallbacks to default permission
            $access = $this->defaultPermissions[$role];
        }

        return $access;
    }

    /**
     * Class constructor.
     *
     * @param \App\Repository\RoleAccessInterface $roleAccessRepository The role access repository
     * @param string                              $resource             The resource
     * @param string                              $accessLevel          The access level
     */
    public function __construct(RoleAccessInterface $roleAccessRepository, string $resource, string $accessLevel) {
        $this->roleAccessRepository     = $roleAccessRepository;
        $this->resource                 = $resource;
        $this->accessLevel              = (int) $accessLevel;

        $this->defaultPermissions   = [
            Role::COMPANY           => RoleAccess::ACCESS_READ | RoleAccess::ACCESS_WRITE | RoleAccess::ACCESS_EXECUTE,
            Role::COMPANY_ADMIN     => RoleAccess::ACCESS_READ,
            Role::COMPANY_OWNER     => RoleAccess::ACCESS_READ,
            Role::COMPANY_MEMBER    => RoleAccess::ACCESS_READ,
            Role::USER              => RoleAccess::ACCESS_READ,
            Role::GUEST             => RoleAccess::ACCESS_READ
        ];
    }

    /**
     * Invoked function when the middleware is called for that route.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request  The request
     * @param \Psr\Http\Message\ResponseInterface      $response The response
     * @param Function|callable                        $next     The next callable object
     *
     * @throws \App\Exception\NotAllowed Throws NotAllowed if the actor doesn't have access to the resource
     *
     * @return Function Next callable function
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next) : ResponseInterface {
        $actingUser     = $request->getAttribute('actingUser');
        $targetUser     = $request->getAttribute('targetUser');
        $actingCompany  = $request->getAttribute('actingCompany');

        $routeName                = $request->getAttribute('route')->getName();
        $allowed                  = false;

        if (! $targetUser) {
            return $next($request, $response);
        }

        // Use cases got by this middleware:
        // User -> User
        //      User (company owner)    ->  User
        //      User (company member)   ->  User
        //      User (company admin)    ->  User
        //      User (any user) -> User
        // Company -> User

        // User -> User
        if ($actingUser && $actingUser->id !== $targetUser->id) {
            // @FIXME When company members are developed get back to this middleware and find the specific role for each use case
            $role = Role::USER;

            $access = $this->getAccessFromRole($targetUser->identityId, $role, $this->resource);

            if (($this->accessLevel & $access) !== $this->accessLevel) {
                throw new NotAllowed();
            }
        }

        // use case: Company -> User
        if ($actingCompany) {
            $role   = Role::COMPANY;
            $access = $this->getAccessFromRole($targetUser->identityId, $role, $this->resource);

            if (($this->accessLevel & $access) !== $this->accessLevel) {
                throw new NotAllowed();
            }
        }

        $request = $this->allow($request);

        return $next($request, $response);
    }

    private function allow(ResponseInterface $response) : ResponseInterface {
        return $response->withHeader('Allowed', 'true');
    }
}
