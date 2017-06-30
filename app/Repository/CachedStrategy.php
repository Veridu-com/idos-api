<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use App\Factory\Repository;
use Stash\Pool;

/**
 * Cache-based Repository Strategy.
 */
class CachedStrategy implements RepositoryStrategyInterface {
    /**
     * Repository Factory.
     *
     * @var \App\Factory\Repository
     */
    private $repositoryFactory;
    /**
     * Cache Pool.
     *
     * @var \Stash\Pool
     */
    private $pool;

    /**
     * Class constructor.
     *
     * @param \App\Factory\Repository $repositoryFactory
     * @param \Stash\Pool             $pool
     *
     * @return void
     */
    public function __construct(Repository $repositoryFactory, Pool $pool) {
        $this->repositoryFactory = $repositoryFactory;
        $this->pool              = $pool;
    }

    /**
     * {@inheritdoc}
     */
    public function getFormattedName(string $repositoryName) : string {
        // return $this->repositoryFactory->getFormattedName($repositoryName);
        static $cache = [];

        if (isset($cache[$repositoryName])) {
            return $cache[$repositoryName];
        }

        $splitName = explode('\\', $repositoryName);

        if (is_array($splitName) && count($splitName) > 1) {
            $name                   = array_pop($splitName);
            $namespace              = implode('\\', $splitName);
            $formattedName          = sprintf('%s\\DB%s', $namespace, ucfirst($name));
            $cache[$repositoryName] = $formattedName;

            return $formattedName;
        }

        $formattedName          = sprintf('DB%s', ucfirst($repositoryName));
        $cache[$repositoryName] = $formattedName;

        return $formattedName;
    }

    /**
     * {@inheritdoc}
     */
    public function build(Repository $repositoryFactory, string $className) : RepositoryInterface {
        return new Cache(
            $this->repositoryFactory->create($className),
            $this->pool
        );
    }
}
