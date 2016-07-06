<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Repository;

/**
 * Permission Repository Interface.
 */
interface PermissionInterface extends RepositoryInterface {
    /**
     * Gets all Permissions based on their Company Id.
     *
     * @param int $companyId
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllByCompanyId($companyId);
    /**
     * Deletes all Permissions based on their Company Id.
     *
     * @param int $companyId
     *
     * @return int
     */
    public function deleteByCompanyId($companyId);
    /**
     * Find one permission based on their companyId, routeName.
     *
     * @param int    $companyId
     * @param string $routeName
     *
     * @return App\Entity\Permission
     */
    public function findOne($companyId, $routeName);
    /**
     * Deletes one permission based on their companyId, routeName.
     *
     * @param int    $companyId
     * @param string $routeName
     *
     * @return App\Entity\Permission
     */
    public function deleteOne($companyId, $routeName);
}
