<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Middleware;

use App\Entity\Role;
use App\Entity\RoleAccess;
use App\Exception\NotAllowed;
use App\Exception\NotFound;
use Interop\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Route Middleware
 * This middleware is responsible to add a "allowed" parameter on the $response object.
 */
class UserPermission implements MiddlewareInterface {
    private $container;
    private $defaultPermissions;
    private $roleAccessRepository;

    public function __construct(ContainerInterface $container, string $resource, string $accessLevel) {
        $this->container            = $container;
        $this->resource             = $resource;
        $this->accessLevel          = (int) $accessLevel;

        $this->defaultPermissions   = [
            Role::COMPANY           => RoleAccess::ACCESS_READ | RoleAccess::ACCESS_WRITE | RoleAccess::ACCESS_EXECUTE,
            Role::COMPANY_ADMIN     => RoleAccess::ACCESS_READ,
            Role::COMPANY_OWNER     => RoleAccess::ACCESS_READ,
            Role::COMPANY_MEMBER    => RoleAccess::ACCESS_READ,
            Role::USER              => RoleAccess::ACCESS_READ,
            Role::GUEST             => RoleAccess::ACCESS_READ
        ];
    }

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
        //      User (company owner) -> User
        //      User (company member) -> User
        //      User (company admin) -> User
        //      User (any user) -> User
        //  
        // Company -> User
        //      

        // User -> User
        if ($actingUser && $actingUser->id !== $targetUser->id) {
            // @FIXME When company members are developed get back to this middleware and find the specific role for each use case
            $role = Role::USER;

            $access = $this->getAcessFromRole($targetUser->identityId, $role, $this->resource);

            if (($this->accessLevel & $access) !== $this->accessLevel) {
                throw new NotAllowed();
            }
        }

        // use case: Company on User
        if ($actingCompany) {
            $role   = Role::COMPANY;
            $access = $this->getAcessFromRole($targetUser->identityId, $role, $this->resource);

            if (($this->accessLevel & $access) !== $this->accessLevel) {
                throw new NotAllowed();
            }
        }

        return $next($request, $response);
    }

    private function getAcessFromRole(int $identityId, string $role, string $resource) : int {
        try {
            $roleAccessRepository   = $this->container->get('repositoryFactory')->create('RoleAccess');
            $roleAccess             = $roleAccessRepository->findOne($identityId, $role, $resource);
            $access                 = $roleAccess->access;
        } catch (NotFound $e) {
            // fallbacks to default permission
            $access = $this->defaultPermissions[$role];
        }

        return $access;
    }
    private function allow(ResponseInterface $response) : ResponseInterface {
        return $response->withHeader('Allowed', 'true');
    }
}
