<?php

/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Repository;

use App\Entity\EntityInterface;
use App\Entity\Setting;
use Illuminate\Support\Collection;

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
    public function findOne($companyId, $section, $propName) : Setting {
        return $this->findOneBy([
            'company_id' => $companyId,
            'section'    => $section,
            'property'   => $propName
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function update(EntityInterface &$entity) : int {
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
    public function getAllByCompanyId($companyId) : Collection {
        return $this->findBy(['company_id' => $companyId]);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllByCompanyIdAndSection($companyId, $section) : Collection {
        return $this->findBy([
            'company_id' => $companyId,
            'section'    => $section
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteOne($companyId, $section, $property) : int {
        return $this->query()
            ->where('company_id', $companyId)
            ->where('section', $section)
            ->where('property', $property)
            ->delete();
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByCompanyId($companyId) : int {
        return $this->deleteByKey('company_id', $companyId);
    }

}
