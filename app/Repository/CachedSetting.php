<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Setting;
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
    public function findOne(int $companyId, string $section, string $propName) : Setting {
        return $this->findOneBy([
            'company_id' => $companyId,
            'section'    => $section,
            'property'   => $propName
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteOne(int $companyId, string $section, string $propName) : int {
        return $this->deleteBy([
            'company_id' => $companyId,
            'section'    => $section,
            'property'   => $propName
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllByCompanyIdAndSection(int $companyId, string $section) : Collection {
        return $this->findBy([
            'company_id' => $companyId,
            'section'    => $section
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function update(Setting &$entity) : int {
        $this->deleteEntityCache($entity);

        return $this->repository->update($entity);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByCompanyId(int $companyId) : int {
        return $this->deleteBy(['company_id' => $companyId]);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllByCompanyId(int $companyId) : Collection {
        return $this->findBy(['company_id' => $companyId]);
    }
}
