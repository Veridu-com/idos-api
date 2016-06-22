<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Repository;

use App\Factory\Entity;
use Stash\Interfaces\PoolInterface;

/**
 * Cache-based Repository Strategy.
 */
class CachedStrategy implements RepositoryStrategyInterface {
    /**
     * Entity Factory.
     *
     * @var App\Factory\Entity
     */
    private $entityFactory;

    /**
     * Cache Pool.
     *
     * @var \Stash\Interfaces\PoolInterface
     */
    private $cachePool;

    /**
     * Class constructor.
     *
     * @param App\Factory\Entity              $entityFactory
     * @param \Stash\Interfaces\PoolInterface $cachePool
     *
     * @return void
     */
    public function __construct(Entity $entityFactory, PoolInterface $cachePool) {
        $this->entityFactory = $entityFactory;
        $this->cachePool     = $cachePool;
    }

    /**
     * {@inheritDoc}
     */
    public function getFormattedName($repositoryName) {
        return sprintf('Cached%s', ucfirst($repositoryName));
    }

    /**
     * {@inheritDoc}
     */
    public function build($className) {
        return new $className($this->entityFactory, $this->cachePool);
    }
}
