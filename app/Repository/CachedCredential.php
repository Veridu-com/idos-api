<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Repository;

use App\Exception\NotFound;
use App\Entity\Credential;
use Stash\Interfaces\PoolInterface;
use Stash\Invalidation;

/**
 * Cache-based Credential Repository Implementation.
 */
class CachedCredential extends AbstractCachedRepository implements CredentialInterface {
    /**
     * Class constructor.
     *
     * @param App\Entity\Credential           $entity
     * @param \Stash\Interfaces\PoolInterface $cachePool
     *
     * @return void
     */
    public function __construct(Credential $entity, PoolInterface $cachePool) {
        $this->entity    = $entity;
        $this->cachePool = $cachePool;
    }

    /**
     * {@inheritDoc}
     */
    public function findByPubKey($pubKey) {
        $item = $this->cachePool->getItem(sprintf('/Key/PubKey/%s', $pubKey));
        $data = $item->get(Invalidation::PRECOMPUTE, 300);
        if ($item->isMiss()) {
            $item->lock();
            $data = parent::findByPubKey($pubKey);
            $item->set($data, self::CACHE_TTL);
        }

        return $data;
    }
}
