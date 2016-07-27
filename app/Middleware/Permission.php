<?php

/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

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
class Permission implements MiddlewareInterface {
    const PUBLIC_ACTION     =    'public';
    const PRIVATE_ACTION    =    'private';

    private $container;
    private $permissionType;

    public function __construct(ContainerInterface $container, $permissionType = self::PRIVATE_ACTION) {
        $this->container      = $container;
        $this->permissionType = $permissionType;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next) : ResponseInterface {
        $actingCompany            = $request->getAttribute('actingCompany');                            // get actingCompany set on Auth middleware
        $permissionRepository     = $this->container->get('repositoryFactory')->create('Permission'); // get permissionRepository for checking
        $routeName                = $request->getAttribute('route')->getName();
        $response                 = $this->allow($response);

        if ($this->permissionType === self::PRIVATE_ACTION) {
            try {
                $permission = $permissionRepository->findOne($actingCompany->id, $routeName);
            } catch (NotFound $e) {
                $response->getBody()->rewind();
                throw new NotAllowed();
            }
        }

        return $next($request, $response);
    }

    private function allow(ResponseInterface $response) : ResponseInterface {
        return $response->withHeader('Allowed', 'true');
    }
}
