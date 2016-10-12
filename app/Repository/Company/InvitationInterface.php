<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository\Company;

use App\Entity\Company\Invitation;
use App\Repository\RepositoryInterface;
use Illuminate\Support\Collection;

/**
 * Invitation Repository Interface.
 */
interface InvitationInterface extends RepositoryInterface {
    /**
     * Finds one by hash property.
     *
     * @param string $hash The hash
     *
     * @return \App\Entity\Company\Invitation
     */
    public function findOneByHash(string $hash) : Invitation;

    /**
     * Gets all by company identifier.
     *
     * @param int $companyId The company identifier
     *
     * @return Illuminate\Support\Collection
     */
    public function getAllByCompanyId(int $companyId) : Collection;
}
