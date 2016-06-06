<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Repository;

use App\Factory\Model;
use Stash\Interfaces\PoolInterface;

/**
 * Cache-based Repository Strategy.
 */
class CachedStrategy implements RepositoryStrategyInterface {
    /**
     * Model Factory.
     *
     * @var App\Factory\Model
     */
    private $modelFactory;

    /**
     * Cache Pool.
     *
     * @var \Stash\Interfaces\PoolInterface
     */
    private $cachePool;

    /**
     * Class constructor.
     *
     * @param App\Factory\Model               $modelFactory
     * @param \Stash\Interfaces\PoolInterface $cachePool
     *
     * @return void
     */
    public function __construct(Model $modelFactory, PoolInterface $cachePool) {
        $this->modelFactory = $modelFactory;
        $this->cachePool    = $cachePool;
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
    public function build($className, $repositoryName) {
        return new $className($this->modelFactory->create($repositoryName), $this->cachePool);
    }
}
