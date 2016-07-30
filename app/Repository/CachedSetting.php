<?php

/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\EntityInterface;
use App\Entity\Setting as SettingEntity;
use Illuminate\Support\Collection;

/**
 * Cache-based Setting Repository Implementation.
 */
class CachedSetting extends AbstractCachedRepository implements SettingInterface {
    /**
     * The entity associated with the repository.
     *
     * @var string
     */
    protected $entityName = 'Setting';

    /**
     * {@inheritdoc}
     */
    public function findOne($companyId, $section, $propName) : SettingEntity {
        return $this->findOneBy([
            'company_id' => $companyId,
            'section'    => $section,
            'property'   => $propName
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteOne($companyId, $section, $propName) : int {
        return $this->deleteBy([
            'company_id' => $companyId,
            'section'    => $section,
            'property'   => $propName
        ]);
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
    public function update(EntityInterface &$entity) : int {
        $this->deleteEntityCache($entity);

        return $this->repository->update($entity);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByCompanyId($companyId) : int {
        return $this->deleteBy(['company_id' => $companyId]);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllByCompanyId($companyId) : Collection {
        return $this->findBy(['company_id' => $companyId]);
    }
}
