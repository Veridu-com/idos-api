<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Repository;

use App\Factory\Entity;

/**
 * Abstract Generic Repository.
 */
abstract class AbstractRepository implements RepositoryInterface {
    /**
     * Entity Factory.
     *
     * @var App\Factory\Entity
     */
    protected $entityFactory;

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
    public function create(array $attributes) {
        $name = sre_replace(__NAMESPACE__, '', __CLASS__);
        return $this->entityFactory->create($name, $attributes);
    }
}
