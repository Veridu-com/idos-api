<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository\Company;

use App\Entity\Company\Member;
use App\Repository\RepositoryInterface;
use Illuminate\Support\Collection;

/**
 * Member Repository Interface.
 */
interface MemberInterface extends RepositoryInterface {
    /**
     * Finds direct or indirectly between an Identity and a Company.
     *
     * @param int $identityId The identity identifier
     * @param int $companyId  The company identifier
     *
     * @return \App\Entity\Company\Member
     */
    public function findMembership(int $identityId, int $companyId) : Member;

    /**
     * Finds a direct membership with a Company and an Identity.
     *
     * @param int $identityId The identity identifier
     * @param int $companyId  The company identifier
     *
     * @throws \App\Exception\NotFound
     *
     * @return \App\Entity\Company\Member
     */
    public function findDirectMembership(int $identityId, int $companyId) : Member;

    /**
     * Gets by company identifier.
     *
     * @param int   $companyId   The company identifier
     * @param array $queryParams The query parameters
     *
     * @return \Illuminate\Support\Collection
     */
    public function getByCompanyId(int $companyId, array $queryParams = []) : Collection;
}
