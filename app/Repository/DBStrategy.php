<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use App\Factory\Entity;
use Illuminate\Database\Connection;
use Jenssegers\Optimus\Optimus;

/**
 * Database-based Repository Strategy.
 */
class DBStrategy implements RepositoryStrategyInterface {
    /**
     * Entity Factory.
     *
     * @var App\Entity\EntityFactory
     */
    public $entityFactory;
    /**
     * DB Connection.
     *
     * @var \Illuminate\Database\Connection
     */
    protected $connection;

    /**
     * Optimus instance.
     *
     * @var \Jenssegers\Optimus\Optimus
     */
    protected $optimus;

    /**
     * Class constructor.
     *
     * @param App\Factory\Entity              $entityFactory
     * @param \Jenssegers\Optimus\Optimus     $optimus
     * @param \Illuminate\Database\Connection $connection
     *
     * @return void
     */
    public function __construct(
        Entity $entityFactory,
        Optimus $optimus,
        Connection $connection
    ) {
        $this->entityFactory = $entityFactory;
        $this->optimus       = $optimus;
        $this->connection    = $connection;
    }

    /**
     * {@inheritdoc}
     */
    public function getFormattedName(string $repositoryName) : string {
        return sprintf('DB%s', ucfirst($repositoryName));
    }

    /**
     * {@inheritdoc}
     */
    public function build(string $className) : RepositoryInterface {
        return new $className($this->entityFactory, $this->optimus, $this->connection);
    }
}
