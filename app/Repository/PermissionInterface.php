<?php

/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\Permission;
use Illuminate\Support\Collection;

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
    public function getAllByCompanyId(int $companyId) : Collection;
    /**
     * Deletes all Permissions based on their Company Id.
     *
     * @param int $companyId
     *
     * @return int
     */
    public function deleteByCompanyId(int $companyId) : int;
    /**
     * Find one permission based on their companyId, routeName.
     *
     * @param int    $companyId
     * @param string $routeName
     *
     * @return App\Entity\Permission
     */
    public function findOne(int $companyId, string $routeName) : Permission;
    /**
     * Deletes one permission based on their companyId, routeName.
     *
     * @param int    $companyId
     * @param string $routeName
     *
     * @return App\Entity\Permission
     */
    public function deleteOne(int $companyId, string $routeName) : int;
}
