<?php

/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Factory;

use App\Repository\RepositoryStrategyInterface;

/**
 * Repository Factory Implementation.
 */
class Repository extends AbstractFactory {
    /**
     * Repository Strategy.
     *
     * @var App\Repository\RepositoryStrategyInterface;
     */
    protected $strategy;

    /**
     * {@inheritdoc}
     */
    protected function getNamespace() {
        return '\\App\\Repository\\';
    }

    /**
     * {@inheritdoc}
     */
    protected function getFormattedName($name) : string {
        return $this->strategy->getFormattedName($name);
    }

    /**
     * Class constructor.
     *
     * @param App\Repository\RepositoryStrategy $strategy
     *
     * @return void
     */
    public function __construct(RepositoryStrategyInterface $strategy) {
        $this->strategy = $strategy;
    }

    /**
     * {@inheritdoc}
     */
    public function create($name) {
        $class = $this->getClassName($name);

        if (class_exists($class))
            return $this->strategy->build($class);

        throw new \RuntimeException(sprintf('"%s" (%s) not found.', $name, $class));
    }
}
