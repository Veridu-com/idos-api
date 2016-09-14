<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\Setting;
use Illuminate\Support\Collection;

/**
 * Setting Repository Interface.
 */
interface SettingInterface extends RepositoryInterface {
    /**
     * Gets all Settings based on their Company Id.
     *
     * @param int $companyId
     *
     * @return array
     */
    public function getAllByCompanyId(int $companyId) : array;

    /**
     * Deletes all Settings based on their Company Id.
     *
     * @param int $companyId
     *
     * @return int
     */
    public function deleteByCompanyId(int $companyId) : int;

    /**
     * Gets all public by company identifier, filters by $queryParams.
     *
     * @param int   $companyId   The company identifier
     * @param array $queryParams The query parameters to filter the collection
     */
    public function getAllPublicByCompanyId(int $companyId, array $queryParams = []) : array;

    /**
     * Returns a collection of settings based on their compani_id.
     *
     * @param int $companyId The company identifier
     *
     * @return \Illuminate\Support\Collection
     */
    public function findByCompanyId(int $companyId) : Collection;

    /**
     * Finds one setting by Company and Setting Id.
     * This method is useful for scoping company access within the settings.
     *
     * @param int $companyId The company identifier
     * @param int $settingId The setting identifier
     */
    public function findOneByCompanyAndId(int $companyId, int $settingId) : Setting;
}
