<?php

/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Setting as SettingEntity;
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
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllByCompanyId($companyId) : Collection;
    /**
     * Deletes all Settings based on their Company Id.
     *
     * @param int $companyId
     *
     * @return int
     */
    public function deleteByCompanyId($companyId) : int;
    /**
     * Find one setting based on their companyId, section and property name.
     *
     * @param int    $companyId
     * @param string $section
     * @param string $propName
     *
     * @return App\Entity\Setting
     */
    public function findOne($companyId, $section, $propName) : SettingEntity;
    /**
     * Deletes one setting based on their companyId, section and property name.
     *
     * @param int    $companyId
     * @param string $section
     * @param string $propName
     *
     * @return int
     */
    public function deleteOne($companyId, $section, $propName) : int;
    /**
     * Retrieves all settings from company that has the given section.
     *
     * @param int    companyId setting's company_id
     * @param string section   setting's section
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllByCompanyIdAndSection($companyId, $section) : Collection;
    /**
     * Updates one setting.
     *
     * @param int    companyId setting's company_id
     * @param string section   setting's section
     * @param string propName  setting's propName
     *
     * @return int
     */
    public function update(\App\Entity\EntityInterface &$entity) :int;
}
