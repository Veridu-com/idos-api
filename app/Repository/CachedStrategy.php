<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Repository;

use App\Factory\Repository;
use Stash\Interfaces\PoolInterface;

/**
 * Cache-based Repository Strategy.
 */
class CachedStrategy implements RepositoryStrategyInterface {
    /**
     * Repository Factory.
     *
     * @var App\Factory\Repository
     */
    private $repositoryFactory;
    /**
     * Cache Pool.
     *
     * @var \Stash\Interfaces\PoolInterface
     */
    private $cachePool;

    /**
     * Class constructor.
     *
     * @param App\Factory\Repository          $repositoryFactory
     * @param \Stash\Interfaces\PoolInterface $cachePool
     *
     * @return void
     */
    public function __construct(Repository $repositoryFactory, PoolInterface $cachePool) {
        $this->repositoryFactory = $repositoryFactory;
        $this->cachePool         = $cachePool;
    }

    /**
     * {@inheritdoc}
     */
    public function getFormattedName($repositoryName) {
        return sprintf('Cached%s', ucfirst($repositoryName));
    }

    /**
     * {@inheritdoc}
     */
    public function build($className) {
        $repositoryName = preg_replace('/^.*?Cached/', '', $className);

        return new $className(
            $this->repositoryFactory->create($repositoryName),
            $this->cachePool
        );
    }
}
