<?php

declare(strict_types=1);
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Repository;

use App\Factory\Entity;
use Illuminate\Database\Connection;

/**
 * Database-based Repository Strategy.
 */
class DBStrategy implements RepositoryStrategyInterface {
    /**
     * Entity Factory.
     *
     * @var App\Entity\EntityFactory
     */
    private $entityFactory;
    /**
     * DB Connection.
     *
     * @var \Illuminate\Database\Connection
     */
    protected $connection;

    /**
     * Class constructor.
     *
     * @param App\Factory\Entity              $entityFactory
     * @param \Illuminate\Database\Connection $connection
     *
     * @return void
     */
    public function __construct(
        Entity $entityFactory,
        Connection $connection
    ) {
        $this->entityFactory = $entityFactory;
        $this->connection    = $connection;
    }

    /**
     * {@inheritdoc}
     */
    public function getFormattedName($repositoryName) {
        return sprintf('DB%s', ucfirst($repositoryName));
    }

    /**
     * {@inheritdoc}
     */
    public function build($className) {
        return new $className($this->entityFactory, $this->connection);
    }
}
