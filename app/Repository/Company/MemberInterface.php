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
     * @return App\Entity\Company\Member
     */
    public function findMembership(int $identityId, int $companyId) : Member;
}
