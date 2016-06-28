<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Repository;

use App\Entity\Setting;
use App\Entity\EntityInterface;
use App\Exception\NotFound;
/**
 * Database-based Setting Repository Implementation.
 */
class DBSetting extends AbstractDBRepository implements SettingInterface {
    /**
     * The table associated with the repository.
     *
     * @var string
     */
    protected $tableName = 'settings';
    /**
     * The entity associated with the repository.
     *
     * @var string
     */
    protected $entityName = 'Setting';

    /**
     * {@inheritdoc}
     */
    public function findByPubKey($pubKey) {
        return $this->findByKey('public', $pubKey);
    }

    /**
     * Find one setting given its identifiers
     *
     * @param int    companyId setting's company_id
     * @param string section   setting's section
     * @param string propName  setting's propName
     * 
     */
    public function findOne($companyId, $section, $propName) {
        return $this->getOneByWhereConstraints([
            'company_id' => $companyId, 
            'section' => $section,
            'property' => $propName
        ]);
    }  

    /**
     * Updates one setting
     *
     * @param int    companyId setting's company_id
     * @param string section   setting's section
     * @param string propName  setting's propName
     * 
     */
    public function update(EntityInterface &$entity) {
        $serialized = $entity->serialize();

        return $this->query()
            ->where('company_id', $entity->company_id)
            ->where('section', $entity->section)
            ->where('property', $entity->property)
            ->update($serialized);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllByCompanyId($companyId) {
        return $this->getAllByKey('company_id', $companyId);
    }

    /**
     * Retrieves all settings from company that has the given section 
     *
     * @param int    companyId setting's company_id
     * @param string section   setting's section
     *
     */
    public function getAllByCompanyIdAndSection($companyId, $section) {
        return $this->getAllByWhereConstraints([
            'company_id' => $companyId, 
            'section' => $section
        ]);
    }


    /**
     * Deletes one settings from company that has the given section 
     *
     * @param int    companyId setting's company_id
     * @param string section   setting's section
     */
    public function deleteOne($companyId, $section, $property) {
        return $this->query()
            ->where('company_id', $companyId)
            ->where('section', $section)
            ->where('property', $property)
            ->delete();
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByCompanyId($companyId) {
        return $this->deleteByKey('company_id', $companyId);
    }
    /**
     * {@inheritdoc}
     */
    public function deleteByPubKey($pubKey) {
        return $this->deleteByKey('public', $pubKey);
    }
}
