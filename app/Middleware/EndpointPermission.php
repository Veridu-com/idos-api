<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Middleware;

use App\Exception\NotAllowed;
use App\Repository\CompanyInterface;
use App\Repository\PermissionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Endpoint Permission Middleware.
 *
 * Enpoint access can be one of:
 * - Public: Access is granted by default
 * - Private: Access must be granted by a Veridu Operator
 * - Self: Access is granted by default to the company itself
 * - Parent: Access is granted by default to company's parents
 *
 * Scope: Route.
 * This middleware is responsible to add an "allowed" parameter on the $response object.
 */
class EndpointPermission implements MiddlewareInterface {
    const PUBLIC_ACTION  = 0x00;
    const SELF_ACTION    = 0x01;
    const PARENT_ACTION  = 0x02;
    const PRIVATE_ACTION = 0x04;

    private $permissionRepository;
    private $companyRepository;
    private $permissionType;

    /**
     * Class constructor.
     *
     * @param App\Repository\PermissionInterface $permissionRepository
     * @param App\Repository\CompanyInterface    $companyRepository
     * @param int                                $permissionType
     *
     * @return void
     */
    public function __construct(
        PermissionInterface $permissionRepository,
        CompanyInterface $companyRepository,
        int $permissionType = self::PRIVATE_ACTION
    ) {
        $this->permissionRepository = $permissionRepository;
        $this->companyRepository    = $companyRepository;
        $this->permissionType       = $permissionType;
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
        /*
         * Tags the response with an "allowed" header,
         * so the GateKeeper middleware lets the response go through
         */
        $response = $response->withHeader('Allowed', 'true');

        // Public Actions do not need permission checking
        if ($this->permissionType === self::PUBLIC_ACTION) {
            return $next($request, $response);
        }

        // get actingCompany set on Auth middleware
        $actingCompany = $request->getAttribute('actingCompany');
        // get current route name
        $routeName = $request->getAttribute('route')->getName();

        $allowed = false;

        if (($this->permissionType & self::PRIVATE_ACTION) === self::PRIVATE_ACTION) {
            // checks if the $actingCompany has access to $routeName
            $allowed = $this->permissionRepository->isAllowed(
                $actingCompany->id,
                $routeName
            );
        }

        if (($this->permissionType & self::SELF_ACTION) === self::SELF_ACTION) {
            $targetCompany = $request->getAttribute('targetCompany');
            if ($targetCompany->id === $actingCompany->id) {
                $allowed = true;
            }
        }

        if ((! $allowed) && ($this->permissionType & self::PARENT_ACTION) === self::PARENT_ACTION) {
            $targetCompany = $request->getAttribute('targetCompany');
            // checks if the $actingCompany is a parent of $targetCompany
            $allowed = $this->companyRepository->isParent(
                $actingCompany,
                $targetCompany
            );
        }

        if (! $allowed) {
            throw new NotAllowed();
        }

        return $next($request, $response);
    }
}
