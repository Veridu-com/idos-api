<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Repository;

use App\Exception\NotFound;
use App\Model\Credential;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Stash\Interfaces\PoolInterface;
use Stash\Invalidation;

/**
 * Cache-based Credential Repository Implementation.
 */
class CachedCredential extends AbstractCachedRepository implements CredentialInterface {
    /**
     * Class constructor.
     *
     * @param App\Model\Credential            $model
     * @param \Stash\Interfaces\PoolInterface $cachePool
     *
     * @return void
     */
    public function __construct(Credential $model, PoolInterface $cachePool) {
        $this->model     = $model;
        $this->cachePool = $cachePool;
    }

    /**
     * {@inheritDoc}
     */
    public function findByPubKey($pubKey) {
        try {
            $item = $this->cachePool->getItem(sprintf('/Key/PubKey/%s', $pubKey));
            $data = $item->get(Invalidation::PRECOMPUTE, 300);
            if ($item->isMiss()) {
                $item->lock();
                $data = $this->model->where('public', $pubKey)->firstOrFail();
                $item->set($data, self::CACHE_TTL);
            }

            return $data;
        } catch (ModelNotFoundException $exception) {
            throw new NotFound(get_class($this->model));
        }
    }
}
