<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Factory;

use App\Repository\Cache;
use App\Repository\RepositoryStrategyInterface;
use Stash\Pool;

/**
 * Repository Factory Implementation.
 */
class Repository extends AbstractFactory {
    /**
     * Repository Strategy.
     *
     * @var \App\Repository\RepositoryStrategyInterface
     */
    protected $strategy;
    /**
     * Cache Pool.
     *
     * @var \Stash\Pool
     */
    private $pool;

    /**
     * {@inheritdoc}
     */
    protected function getNamespace() : string {
        return '\\App\\Repository\\';
    }

    /**
     * {@inheritdoc}
     */
    protected function getFormattedName(string $name) : string {
        return $this->strategy->getFormattedName($name);
    }

    /**
     * Class constructor.
     *
     * @param \App\Repository\RepositoryStrategyInterface $strategy
     * @param \Stash\Pool                                 $pool
     *
     * @return void
     */
    public function __construct(RepositoryStrategyInterface $strategy, ? Pool $pool = null) {
        $this->strategy = $strategy;
        $this->pool     = $pool;
    }

    /**
     * {@inheritdoc}
     */
    public function create(string $name) {
        $class = $this->getClassName($name);

        if (class_exists($class)) {
            $repository = $this->strategy->build($this, $class);
            if ($this->pool === null) {
                return $repository;
            }

            return new Cache(
                $repository,
                $this->pool
            );
        }

        throw new \RuntimeException(sprintf('"%s" (%s) not found.', $name, $class));
    }
}
