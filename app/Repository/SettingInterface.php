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
     * Retrieves all settings from company that has the given section.
     *
     * @param int    companyId setting's company_id
     * @param string section   setting's section
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllByCompanyIdAndSection(int $companyId, string $section) : Collection;

    /**
     * Updates one setting.
     *
     * @param App\Entity\Setting $setting instance
     *
     * @return int
     */
    public function update(Setting &$setting) : int;
}
