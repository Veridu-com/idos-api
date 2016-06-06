<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Repository;

use App\Exception\NotFound;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Stash\Invalidation;

/**
 * Abstract Cache-based Repository.
 */
abstract class AbstractCachedRepository extends AbstractRepository {
    /**
     * Cache Pool Instance.
     *
     * @var Stash\Interfaces\PoolInterface
     */
    protected $cachePool;

    /**
     * @const CACHE_TTL Cache TTL
     */
    const CACHE_TTL = 3600;

    /**
     * {@inheritDoc}
     */
    public function find($id) {
        try {
            $item = $this->cachePool->getItem(sprintf('Company/%d', $id));
            $data = $item->get(Invalidation::PRECOMPUTE, 300);
            if ($item->isMiss()) {
                $item->lock();
                $data = $this->model->findOrFail($id);
                $item->set($data);
                $item->expiresAfter(self::CACHE_TTL);
                $this->cachePool->save($item);
            }

            return $data;
        } catch (ModelNotFoundException $exception) {
            throw new NotFound(get_class($this->model));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getAll() {
        try {
            $collection = $this->cachePool->getItem('Companies');
            $data       = $collection->get(Invalidation::PRECOMPUTE, 300);
            if ($collection->isMiss()) {
                $collection->lock();
                $data = $this->model->all();
                $collection->set($data);
                $collection->expiresAfter(self::CACHE_TTL);
                $this->cachePool->save($collection);
            }

            return $data;
        } catch (ModelNotFoundException $exception) {
            throw new NotFound(get_class($this->model));
        }
    }
}
