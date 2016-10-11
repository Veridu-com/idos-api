<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Middleware;

use App\Entity\Company\Member;
use App\Exception\NotAllowed;
use App\Repository\Company\PermissionInterface;
use App\Repository\CompanyInterface;
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
     * @param \App\Repository\Company\PermissionInterface $permissionRepository
     * @param \App\Repository\CompanyInterface            $companyRepository
     * @param int                                         $permissionType
     *
     * @return void
     */
    public function __construct(
        PermissionInterface $permissionRepository,
        CompanyInterface $companyRepository,
        int $permissionType = self::SELF_ACTION,
        int $allowedRolesBits = 0x00
    ) {
        $this->permissionRepository = $permissionRepository;
        $this->allowedRolesBits     = $allowedRolesBits;
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

        // get identity set on Auth middleware
        $identity = $request->getAttribute('identity');
        if ($identity) {
            $identityMembers = $identity->member();
        }

        $targetCompany   = $request->getAttribute('targetCompany');
        $hasParentAccess = false;
        $allowed         = false;

        if (! $targetCompany) {
            return $next($request, $response);
        }

        // get current route name
        $routeName = $request->getAttribute('route')->getName();

        // How can an identity access this resource?
        //
        // 1. If PRIVATE ACTION (does this make sense still?) @FIXME
        //      - 1.1 Can one of the identity's companies access this resource?
        //          - Is the identity rank < $this->allowedRolesBits in any company?
        //          - if yep, check if has a register on DBPermission for this company and this routeName
        //          - if yep, check if that company is a parent or is the target company.
        // Remove this in case of keeping PRIVATE_ACTION
        if ($this->permissionType === self::PRIVATE_ACTION) {
            return $next($request, $response);
        }

        // 2. If PARENT ACTION
        //      - 2.1 Can one of the identity's companies access this resource?
        //          - Is the identity rank < 3 in any company?
        //          - if yep, check if that company is a parent of the target company.
        //
        // 2. If SELF ACTION
        //      - 2.1 Can one of the identity's companies access this resource?
        //          - Is the identity rank < 3 in any company?
        //          - if yep, check if that company is the target company.
        //
        // @FIXME delete this?
        // if (($this->permissionType & self::PRIVATE_ACTION) === self::PRIVATE_ACTION) {
        //     // checks if the $company has access to $routeName
        //     $allowed = $this->permissionRepository->isAllowed(
        //         $company->id,
        //         $routeName
        //     );
        // }

        if (($this->permissionType & self::SELF_ACTION) === self::SELF_ACTION) {
            // searches for a membership within the company
            foreach ($identityMembers as $member) {
                if ($this->roleHasAccess($member->role()->bit) && $member->company()->id == $targetCompany->id) {
                    $allowed = true;
                }
            }
        }

        // this has to be called always it is set to the endpoint
        // since we need $hasParentAccess variable to be set on the $request variable
        if (($this->permissionType & self::PARENT_ACTION) === self::PARENT_ACTION) {
            // searches for a membership within the company
            foreach ($identityMembers as $member) {
                if ($this->roleHasAccess($member->role()->bit)
                    && ($this->companyRepository->isParent($member->company(), $targetCompany)
                    || $this->isVeriduMember($member))
                ) {
                    $allowed         = true;
                    $hasParentAccess = true;
                }
            }
        }

        $request = $request->withAttribute('hasParentAccess', $hasParentAccess);

        if (! $allowed) {
            throw new NotAllowed();
        }

        return $next($request, $response);
    }

    /**
     * Returns if role is contained on instance bitmask.
     *
     * @param int $role The role
     */
    private function roleHasAccess(int $role) : bool {
        return ($this->allowedRolesBits & $role) === $role;
    }

    /**
     * Determines if Member is a Veridu Member.
     * This is needed for having Veridu protected settings visible.
     *
     * @param \App\Entity\Company\Member $member The member
     */
    private function isVeriduMember(Member $member) : bool {
        return $member->company()->id === 1;
    }
}
