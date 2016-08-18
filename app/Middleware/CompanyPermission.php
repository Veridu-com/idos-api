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
    const PUBLIC_ACTION  = 0x00;
    const SELF_ACTION    = 0x01;
    const PARENT_ACTION  = 0x02;
    const PRIVATE_ACTION = 0x04;

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
        $actingCompany = $request->getAttribute('actingCompany');
        // get permissionRepository for checking
        $permissionRepository     = $this->container->get('repositoryFactory')->create('Permission');
        $routeName                = $request->getAttribute('route')->getName();
        $response                 = $this->allow($response);
        
        $allowed = true;

        if (($this->permissionType & self::PRIVATE_ACTION) === self::PRIVATE_ACTION) {
            try {
                $permission = $permissionRepository->findOne($actingCompany->id, $routeName);
            } catch (NotFound $e) {
                // deny
                throw new NotAllowed;
             }
        }
        
        if (($this->permissionType & self::SELF_ACTION) === self::SELF_ACTION) {
            $targetCompany = $request->getAttribute('targetCompany');
            if ($targetCompany->id !== $actingCompany->id) {
                // deny
                $allowed = false;
            }
        }
        
        if (($this->permissionType & self::PARENT_ACTION) === self::PARENT_ACTION) {
            $targetCompany = $request->getAttribute('targetCompany');
            $companyRepository     = $this->container->get('repositoryFactory')->create('Company');
            // deny or allow
            $allowed = $companyRepository->isParent($actingCompany, $targetCompany);
        }

        if (! $allowed) {
            throw new NotAllowed;
        }


        return $next($request, $response);
    }

    private function allow(ResponseInterface $response) : ResponseInterface {
        return $response->withHeader('Allowed', 'true');
    }
}
