<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\Company;
use App\Entity\Handler;
use App\Exception\NotFound;
use Illuminate\Support\Collection;

/**
 * Database-based Handler Repository Implementation.
 */
class DBHandler extends AbstractSQLDBRepository implements HandlerInterface {
    /**
     * The table associated with the repository.
     *
     * @var string
     */
    protected $tableName = 'handlers';
    /**
     * The entity associated with the repository.
     *
     * @var string
     */
    protected $entityName = 'Handler';

    /**
     * {@inheritdoc}
     */
    protected $filterableKeys = [
        'created_at' => 'date'
    ];

    /**
     * {@inheritdoc}
     */
    public function getByCompanyId(int $companyId, array $queryParams = []) : Collection {
        return $this->findBy(['company_id' => $companyId], $queryParams);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllByCompanyId(int $companyId) : Collection {
        return $this->findBy(
            [
                'company_id' => $companyId,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function findByPubKey(string $key) : Handler {
        return $this->findOneBy(['public' => $key]);
    }

    /**
     * {@inheritdoc}
     */
    public function findByPrivKey(string $key) : Handler {
        return $this->findOneBy(['private' => $key]);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteOne(int $handlerId, Company $company) : int {
        $affectedRows = $this->query()
            ->where('id', $handlerId)
            ->where('company_id', $company->id)
            ->delete();

        if (! $affectedRows) {
            throw new NotFound();
        }

        return $affectedRows;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByCompanyId(int $companyId) : int {
        $affectedRows = $this->deleteBy(
            [
                'company_id' => $companyId
            ]
        );

        return $affectedRows;
    }
}
