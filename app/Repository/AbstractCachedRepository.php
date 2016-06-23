<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Repository;

use App\Entity\EntityInterface;
use Stash\Interfaces\PoolInterface;
use Stash\Invalidation;

/**
 * Abstract Cache-based Repository.
 */
abstract class AbstractCachedRepository extends AbstractRepository {
    /**
     * Repository Instance.
     *
     * @var App\Repository\RepositoryInterface
     */
    protected $repository;
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
     * Gets the cache key for a key.
     *
     * @param string $key
     *
     * @return string
     */
    protected function getCacheKey($key) {
        $item     = $this->cachePool->getItem(sprintf('/keys/%s', $key));
        $cacheKey = $item->get(Invalidation::PRECOMPUTE, 300);
        if ($item->isMiss()) {
            $item->lock();
            $cacheKey = sprintf('_%s', time());
            $item->set($cacheKey);
            $item->expiresAfter(self::CACHE_TTL);
            $this->cachePool->save($item);
        }

        return $cacheKey;
    }

    /**
     * Invalidates cache content for a key.
     *
     * @param string $key
     *
     * @return void
     */
    protected function invalidateCache($key) {
        $item = $this->cachePool->getItem(sprintf('/keys/%s', $key));
        if (! $item->isMiss()) {
            $item->lock();
            $item->set(sprintf('_%s', time()));
            $item->expiresAfter(self::CACHE_TTL);
            $this->cachePool->save($item);
        }
    }

    /**
     * Class constructor.
     *
     * @param App\Repository\RepositoryInterface $repository
     * @param \Stash\Interfaces\PoolInterface    $cachePool
     *
     * @return void
     */
    public function __construct(
        RepositoryInterface $repository,
        PoolInterface $cachePool
    ) {
        $this->repository = $repository;
        $this->cachePool  = $cachePool;
    }

    /**
     * {@inheritdoc}
     */
    public function save(EntityInterface &$entity) {
        $this->repository->save($entity);
    }
}
