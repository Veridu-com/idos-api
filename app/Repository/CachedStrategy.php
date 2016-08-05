<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use Apix\Cache\PsrCache\TaggablePool;
use App\Factory\Repository;

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
     * @var \Apix\Cache\PsrCache\TaggablePool
     */
    private $cache;

    /**
     * Class constructor.
     *
     * @param App\Factory\Repository            $repositoryFactory
     * @param \Apix\Cache\PsrCache\TaggablePool $cache
     *
     * @return void
     */
    public function __construct(Repository $repositoryFactory,  TaggablePool $cache) {
        $this->repositoryFactory = $repositoryFactory;
        $this->cache             = $cache;
    }

    /**
     * {@inheritdoc}
     */
    public function getFormattedName(string $repositoryName) : string {
        return sprintf('Cached%s', ucfirst($repositoryName));
    }

    /**
     * {@inheritdoc}
     */
    public function build(string $className) : RepositoryInterface {
        $repositoryName = preg_replace('/.*?Cached/', '', $className);

        return new $className(
            $this->repositoryFactory->create($repositoryName),
            $this->cache
        );
    }
}
