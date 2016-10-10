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
     * Finds one membership by identity and company ids.
     *
     * @param      integer  $identityId  The identity identifier
     * @param      integer  $companyId   The company identifier
     *
     * @return     App\Entity\Company\Member
     */
    public function findMembership(int $identityId, int $companyId) : Member;

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
     * @return App\Entity\Company\Member
     */
    public function findOne(int $memberId);

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
