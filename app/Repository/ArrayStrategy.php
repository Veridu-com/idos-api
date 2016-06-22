<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Repository;

use App\Factory\Entity;

/**
 * Array-based Repository Strategy.
 */
class ArrayStrategy implements RepositoryStrategyInterface {
    /**
     * Entity Factory.
     *
     * @var App\Factory\Entity
     */
    private $entityFactory;

    /**
     * Class constructor.
     *
     * @param App\Factory\Entity $entityFactory
     *
     * @return void
     */
    public function __construct(Entity $entityFactory) {
        $this->entityFactory = $entityFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function getFormattedName($repositoryName) {
        return sprintf('Array%s', ucfirst($repositoryName));
    }

    /**
     * {@inheritDoc}
     */
    public function build($className) {
        return new $className($this->entityFactory);
    }
}
