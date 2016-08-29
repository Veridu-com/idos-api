<?php
/*
w * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\Permission;
use Illuminate\Support\Collection;

/**
 * Cache-based Permission Repository Implementation.
 */
class CachedPermission extends AbstractCachedRepository implements PermissionInterface {
    /**
     * {@inheritdoc}
     */
    public function findOne(int $companyId, string $routeName) : Permission {
        return $this->findOneBy(
            [
            'company_id' => $companyId,
            'route_name' => $routeName
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getAllByCompanyId(int $companyId) : Collection{
        return $this->findOneBy(['company_id' => $companyId]);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteOne(int $companyId, string $routeName) : int {
        return $this->findBy('company_id', $companyId);
    }

    /**
     * Deletes one permissions from company.
     *
     * @param int    companyId permission's company_id
     * @param string routeName   permission's routeName
     */
    public function deleteOne(int $companyId, string $routeName) : int {
        return $this->deleteBy(
            'company_id' => $companyId,
            'route_name' => $routeName
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByCompanyId(int $companyId) : int {
        return $this->deleteBy(['company_id' => $companyId]);
    }
}
