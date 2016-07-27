<?php

declare(strict_types=1);
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Repository;

use Illuminate\Support\Collection;

/**
 * Cache-based Company Repository Implementation.
 */
class CachedCompany extends AbstractCachedRepository implements CompanyInterface {
    /**
     * The entity associated with the repository.
     *
     * @var string
     */
    protected $entityName = 'Company';
    /**
     * {@inheritdoc}
     */
    public function findByPubKey($publicKey) {
        return $this->findOneBy(['public_key' => $publicKey]);
    }

    /**
     * {@inheritdoc}
     */
    public function findByPrivKey($privateKey) {
        return $this->findOneBy(['private_key' => $privateKey]);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllByParentId($parentId) : Collection {
        return $this->findBy(['parent_id' => $parentId]);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByParentId(int $parentId) : int {
        $entities = $this->findBy(['parent_id' => $parentId]);
        $this->deleteEntitiesFromCache($entities);

        return $this->repository->deleteByParentId($parentId);
    }

}
