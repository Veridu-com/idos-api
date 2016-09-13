<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\Company;
use App\Entity\Service;
use App\Exception\NotFound;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;

/**
 * Database-based Service Repository Implementation.
 */
class DBService extends AbstractSQLDBRepository implements ServiceInterface {
    /**
     * The table associated with the repository.
     *
     * @var string
     */
    protected $tableName = 'services';
    /**
     * The entity associated with the repository.
     *
     * @var string
     */
    protected $entityName = 'Service';

    /**
     * {@inheritdoc}
     */
    protected $filterableKeys = [
        'created_at' => 'date'
    ];

    /**
     * {@inheritdoc}
     */
    public function getAllByCompany(Company $company, array $queryParams = []) : Collection {
        $query = $this->query();
        $query = $this->scopeQuery($query, $company);
        $query = $this->filter($query, $queryParams);

        return $query->get();

        return $query->get();
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
    public function findOne(int $serviceId, Company $company) : Service {
        $query = $this->query()->where('id', $serviceId);
        $query = $this->scopeQuery($query, $company);

        $entity = $query->first();

        if (! $entity) {
            throw new NotFound();
        }

        return $entity;
    }

    /**
     * {@inheritdoc}
     */
    public function findByPubKey(string $key) : Service {
        return $this->findOneBy(['public' => $key]);
    }

    /**
     * {@inheritdoc}
     */
    public function findByPrivKey(string $key) : Service {
        return $this->findOneBy(['private' => $key]);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteOne(int $serviceId, Company $company) : int {
        $affectedRows = $this->query()
            ->where('id', $serviceId)
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

    /**
     * Scopes query given the service "access" attribute.
     *
     * @param \Illuminate\Database\Query\Builder $query   The query
     * @param \App\Entity\Company                $company The company
     *
     * @return Illuminate\Database\Query\Builder The mutated $query
     */
    private function scopeQuery(Builder $query, Company $company) : Builder {
        return $query->where(
            function ($q) use ($company) {
                // or Visible because it's yours
                $q->orWhere('company_id', $company->id);
                // or Visible because access = 'public'
                $q->orWhere('access', Service::ACCESS_PUBLIC);

                if ($company->parentId) {
                    // or Visible because it belongs to parent and has "protected" access
                    $q->orWhere(
                        function ($q1) use ($company) {
                            $q1->where('company_id', $company->parentId);
                            $q1->where('access', Service::ACCESS_PROTECTED);
                        }
                    );
                }
            }
        );
    }
}
