<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Middleware;

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
    

    public function __construct(ContainerInterface $container, string $resource) {
        $this->container            = $container;
        $this->resource             = $resource;
        $this->defaultPermissions   = [
            'company' => 'rw',
            'company.owner' => 'r',
            'company.admin' => 'r',
            'company.member' => 'r',
            'user' => 'r',
            'guest' => 'r'
        ];
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next) : ResponseInterface {
        $actingUser               = $request->getAttribute('actingUser');                            // get actingUser set on Auth middleware
        $permissionRepository     = $this->container->get('repositoryFactory')->create('RoleAccess');   // get permissionRepository for checking
        $routeName                = $request->getAttribute('route')->getName();

        return $next($request, $response);
    }

    private function allow(ResponseInterface $response) : ResponseInterface {
        return $response->withHeader('Allowed', 'true');
    }
}