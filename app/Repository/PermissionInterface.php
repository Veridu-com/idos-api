<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Repository;

/**
 * Permission Repository Interface.
 */
interface PermissionInterface extends RepositoryInterface {
    /**
     * Finds permissions based on their company_id.
     *
     * @param string $companyId
     *
     * @throws App\Exception\NotFound
     *
     * @return \App\Entity\Permission
     */
    public function getAllByCompanyId($companyId);

    /**
     * Delete Permissions based on their company_id.
     *
     * @param string $companyId
     *
     * @throws App\Exception\NotFound
     *
     * @return \App\Entity\Permission
     */
    public function deleteByCompanyId($companyId);
}
