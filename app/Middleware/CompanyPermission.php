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
 * Permission Middleware.
 *
 * Scope: Route.
 * This middleware is responsible to add a "allowed" parameter on the $response object.
 *
 * -FIXME Remove Container injection!
 */
class CompanyPermission implements MiddlewareInterface {
    const SELF_ACTION    =    'self';
    const PARENT_ACTION  =    'parent';
    const PUBLIC_ACTION  =    'public';
    const PRIVATE_ACTION =    'private';

    private $container;
    private $permissionType;

    public function __construct(ContainerInterface $container, $permissionType = self::PRIVATE_ACTION) {
        $this->container      = $container;
        $this->permissionType = $permissionType;
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     * @param callable                                 $next
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next
    ) : ResponseInterface {
        // get actingCompany set on Auth middleware
        $actingCompany            = $request->getAttribute('actingCompany');
        // get permissionRepository for checking
        $permissionRepository     = $this->container->get('repositoryFactory')->create('Permission');
        $routeName                = $request->getAttribute('route')->getName();
        $response                 = $this->allow($response);

        if ($this->permissionType === self::PRIVATE_ACTION) {
            try {
                $permission = $permissionRepository->findOne($actingCompany->id, $routeName);
            } catch (NotFound $e) {
                throw new NotAllowed;
            }
        }
        
        if ($this->permissionType === self::SELF_ACTION || $this->permissionType === self::SELF_OR_PARENT_ACTION) {
            $allowed = false;
            $targetCompany = $request->getAttribute('targetCompany');

            if ($targetCompany->id == $actingCompany->id) {
                $allowed = true;
            } else {
                // if it is a parent
                $companyRepository     = $this->container->get('repositoryFactory')->create('Company');
                $allowed = $companyRepository->isParent($actingCompany, $targetCompany);
            }

            if (! $allowed) {
                throw new NotAllowed;
            }

        }

        return $next($request, $response);
    }

    private function allow(ResponseInterface $response) : ResponseInterface {
        return $response->withHeader('Allowed', 'true');
    }
}
