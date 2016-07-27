<?php

declare(strict_types=1);
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */
namespace App\Repository;

use App\Entity\Permission;
use Illuminate\Support\Collection;

/**
 * Database-based Permission Repository Implementation.
 */
class DBPermission extends AbstractDBRepository implements PermissionInterface {
    /**
     * The table associated with the repository.
     *
     * @var string
     */
    protected $tableName = 'permissions';
    /**
     * The entity associated with the repository.
     *
     * @var string
     */
    protected $entityName = 'Permission';

    /**
     * {@inheritdoc}
     */
    public function findOne(int $companyId, string $routeName) : Permission {
        return $this->findOneBy([
            'company_id' => $companyId,
            'route_name' => $routeName
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllByCompanyId(int $companyId) : Collection{
        return $this->findBy(['company_id' => $companyId]);
    }

    /**
     * Deletes one permissions from company.
     *
     * @param int    companyId permission's company_id
     * @param string routeName   permission's routeName
     *
     * @return int
     */
    public function deleteOne(int $companyId, string $routeName) : int {
        return $this->query()
            ->where('company_id', $companyId)
            ->where('route_name', $routeName)
            ->delete();
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByCompanyId(int $companyId) : int {
        return $this->deleteByKey('company_id', $companyId);
    }

}
