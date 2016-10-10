<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
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
     * Gets all Members based on their Company Id.
     *
     * @param int $companyId
     *
     * @return \Illuminate\Database\Eloquent\Collection
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

    /*
     * Deletes all Members based on their Company Id.
     *
     * @param int $companyId
     *
     * @return int
     */
    public function deleteByCompanyId(int $companyId) : int;

    /**
     * Find one member based on their companyId and username.
     *
     * @param int $memberId
     *
     * @return \App\Entity\Company\Member
     */
    public function findOne(int $memberId);

    /**
     * Saves a member.
     *
     * @param \App\Entity\Company\Member $member The member
     *
     * @return \App\Entity\Company\Member
     */
    public function saveOne(Member $member) : Member;

    /**
     * Deletes one member from company.
     *
     * @param int    $companyId member's company_id
     * @param string $userId    member's username
     *
     * @return int
     */
    public function deleteOne(int $companyId, int $userId) : int;
}
