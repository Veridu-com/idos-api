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
 * Cache-based Permission Repository Implementation.
 */
class CachedPermission extends AbstractCachedRepository implements PermissionInterface {
    /**
     * {@inheritdoc}
     */
    public function findOne(int $companyId, string $routeName) : Permission {
        return $this->repository->findOne($companyId, $routeName);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllByCompanyId(int $companyId) : Collection{
        return $this->repository->getAllByCompanyId($companyId);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteOne(int $companyId, string $routeName) : int {
        return $this->repository->deleteOne($companyId, $routeName);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByCompanyId(int $companyId) : int {
        return $this->repository->deleteByCompanyId($companyId);
    }

}
