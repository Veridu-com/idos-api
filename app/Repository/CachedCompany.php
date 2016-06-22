<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Repository;

use App\Exception\NotFound;
use App\Entity\Company;
use Stash\Interfaces\PoolInterface;
use Stash\Invalidation;

/**
 * Cache-based Company Repository Implementation.
 */
class CachedCompany extends AbstractCachedRepository implements CompanyInterface {
    /**
     * Class constructor.
     *
     * @param App\Entity\Company              $entity
     * @param \Stash\Interfaces\PoolInterface $cachePool
     *
     * @return void
     */
    public function __construct(Company $entity, PoolInterface $cachePool) {
        $this->entity     = $entity;
        $this->cachePool = $cachePool;
    }

    /**
     * {@inheritDoc}
     */
    public function findBySlug($slug) {
        $item = $this->cachePool->getItem(sprintf('/Company/%s', $slug));
        $data = $item->get(Invalidation::PRECOMPUTE, 300);
        if ($item->isMiss()) {
            $item->lock();
            $data = parent::findBySlug($slug);
            $item->set($data, self::CACHE_TTL);
        }

        return $data;
    }

    /**
     * {@inheritDoc}
     */
    public function findByPubKey($pubKey) {
    }

    /**
     * {@inheritDoc}
     */
    public function findByPrivKey($privKey) {
    }

    /**
     * {@inheritDoc}
     */
    public function findByParentId($parentId) {
    }

    /**
     * {@inheritDoc}
     */
    public function deleteByParentId($parentId) {
    }
}
