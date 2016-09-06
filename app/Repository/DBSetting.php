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
 * Database-based Setting Repository Implementation.
 */
class DBSetting extends AbstractSQLDBRepository implements SettingInterface {
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
    protected $filterableKeys = [
        'section'    => 'string',
        'property'   => 'string',
        'created_at' => 'date'
    ];

    /**
     * {@inheritdoc}
     */
    public function getAllByCompanyId(int $companyId, array $queryParams = []) : array {
        $dbQuery = $this->query()->where('company_id', $companyId);

        return $this->paginate(
            $this->filter($dbQuery, $queryParams),
            $queryParams
        );
    }

    /**
     * {@inheritdoc}
     */
    public function findByCompanyId(int $companyId) : Collection {
        return $this->findBy(
            [
                'company_id' => $companyId
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getAllByCompanyIdAndSection(int $companyId, string $section) : Collection {
        return $this->findBy(
            [
            'company_id' => $companyId,
            'section'    => $section
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByCompanyId(int $companyId) : int {
        return $this->deleteByKey('company_id', $companyId);
    }
}
