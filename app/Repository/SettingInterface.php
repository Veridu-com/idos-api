<?php

declare(strict_types=1);
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Repository;

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
    public function getAllByCompanyId($companyId);
    /**
     * Deletes all Settings based on their Company Id.
     *
     * @param int $companyId
     *
     * @return int
     */
    public function deleteByCompanyId($companyId);
    /**
     * Find one setting based on their companyId, section and property name.
     *
     * @param int    $companyId
     * @param string $section
     * @param string $propName
     *
     * @return App\Entity\Setting
     */
    public function findOne($companyId, $section, $propName);
    /**
     * Deletes one setting based on their companyId, section and property name.
     *
     * @param int    $companyId
     * @param string $section
     * @param string $propName
     *
     * @return App\Entity\Setting
     */
    public function deleteOne($companyId, $section, $propName);
    /**
     * Retrieves all settings from company that has the given section.
     *
     * @param int    companyId setting's company_id
     * @param string section   setting's section
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllByCompanyIdAndSection($companyId, $section);
    /**
     * Updates one setting.
     *
     * @param int    companyId setting's company_id
     * @param string section   setting's section
     * @param string propName  setting's propName
     *
     * @return int
     */
    public function update(\App\Entity\EntityInterface &$entity);
}
