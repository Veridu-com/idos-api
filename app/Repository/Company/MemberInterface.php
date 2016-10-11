<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository\Company;

use App\Entity\Company\Member;
use App\Repository\RepositoryInterface;

/**
 * Member Repository Interface.
 */
interface MemberInterface extends RepositoryInterface {
    /**
     * Finds one membership by identity and company ids.
     *
     * @param int $identityId The identity identifier
     * @param int $companyId  The company identifier
     *
     * @return \App\Entity\Company\Member
     */
    public function findMembership(int $identityId, int $companyId) : Member;

    /**
     * Gets all by company identifier.
     *
     * @param      integer  $companyId    The company identifier
     * @param      array    $queryParams  The query parameters
     * 
     * @return \Illuminate\Support\Collection
     */
    public function getAllByCompanyId(int $companyId, array $queryParams = []) : Collection;

    /**
     * Gets all Members basedon their Company Id and role.
     *
     * @param int    companyId member's company_id
     * @param string role  member's role
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAllByCompanyIdAndRole(int $companyId, array $role) : Collection;

    /**
     * Deletes all Members based on their Company Id.
     *
     * @param int $companyId
     *
     * @return int
     */
    public function deleteByCompanyId(int $companyId) : int;
}
