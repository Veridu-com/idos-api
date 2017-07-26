<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository\Company;

use App\Entity\Company\Setting;
use App\Repository\RepositoryInterface;
use Illuminate\Support\Collection;

/**
 * Setting Repository Interface.
 */
interface SettingInterface extends RepositoryInterface {
    /**
     * Retrieves a setting by its section and property.
     *
     * @param int    $companyId  The company identification
     * @param string $section    The section
     * @param array  $properties The properties
     *
     * @return \Illuminate\Support\Collection
     */
    public function findByCompanyIdSectionAndProperties(int $companyId, string $section, array $properties) : Collection;

    /**
     * Finds one setting by Company and Setting Id.
     * This method is useful for scoping company access within the settings.
     *
     * @param int $companyId The company identifier
     * @param int $settingId The setting identifier
     *
     * @return \App\Entity\Company\Setting
     */
    public function findOneByCompanyAndId(int $companyId, int $settingId) : Setting;

    /**
     * Return settings based on their company id.
     *
     * @param int $companyId
     *
     * @return array
     */
    public function getByCompanyId(int $companyId) : array;

    /**
     * Return settings based on their company id.
     *
     * @param int   $companyId   The company identifier
     * @param array $queryParams The query parameters to filter the collection
     *
     * @return array
     */
    public function getPublicByCompanyId(int $companyId, array $queryParams = []) : array;

    /**
     * Returns a collection of settings based on their compani_id.
     *
     * @param int $companyId The company identifier
     *
     * @return \Illuminate\Support\Collection
     */
    public function findByCompanyId(int $companyId) : Collection;

    /**
     * Delete settings based on their company id.
     *
     * @param int $companyId
     *
     * @return int
     */
    public function deleteByCompanyId(int $companyId) : int;
}
