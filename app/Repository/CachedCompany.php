<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Repository;

use App\Exception\NotFound;
use App\Model\Company;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Stash\Interfaces\PoolInterface;
use Stash\Invalidation;

/**
 * Cache-based Company Repository Implementation.
 */
class CachedCompany extends AbstractCachedRepository implements CompanyInterface {
    /**
     * Class constructor.
     *
     * @param App\Model\Company               $model
     * @param \Stash\Interfaces\PoolInterface $cachePool
     *
     * @return void
     */
    public function __construct(Company $model, PoolInterface $cachePool) {
        $this->model     = $model;
        $this->cachePool = $cachePool;
    }

    /**
     * {@inheritDoc}
     */
    public function findBySlug($slug) {
        try {
            $item = $this->cachePool->getItem(sprintf('/Company/%s', $slug));
            $data = $item->get(Invalidation::PRECOMPUTE, 300);
            if ($item->isMiss()) {
                $item->lock();
                $data = $this->model->where('slug', $slug)->firstOrFail();
                $item->set($data, self::CACHE_TTL);
            }

            return $data;
        } catch (ModelNotFoundException $exception) {
            throw new NotFound(get_class($this->model));
        }
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
