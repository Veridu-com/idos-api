<?php

declare(strict_types=1);
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Repository;

use App\Entity\EntityInterface;

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
    public function findOne($companyId, $section, $propName) {
        return $this->findOneBy([
            'company_id' => $companyId,
            'section'    => $section,
            'property'   => $propName
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteOne($companyId, $section, $propName) {
        return $this->deleteBy([
            'company_id' => $companyId,
            'section'    => $section,
            'property'   => $propName
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllByCompanyIdAndSection($companyId, $section) {
        return $this->findBy([
            'company_id' => $companyId,
            'section'    => $section
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function update(EntityInterface &$entity) {
        $this->deleteEntityCache($entity);

        return $this->repository->update($entity);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByCompanyId($companyId) {
        return $this->deleteBy(['company_id' => $companyId]);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllByCompanyId($companyId) {
        return $this->findBy(['company_id' => $companyId]);
    }
}
