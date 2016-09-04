<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use App\Factory\Entity;
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
     * DB Connections.
     *
     * @var array
     */
    protected $connections;

    /**
     * Optimus instance.
     *
     * @var \Jenssegers\Optimus\Optimus
     */
    protected $optimus;

    /**
     * Class constructor.
     *
     * @param App\Factory\Entity          $entityFactory
     * @param \Jenssegers\Optimus\Optimus $optimus
     * @param array                       $connections
     *
     * @return void
     */
    public function __construct(
        Entity $entityFactory,
        Optimus $optimus,
        array $connections
    ) {
        $this->entityFactory = $entityFactory;
        $this->optimus       = $optimus;
        $this->connections   = $connections;
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
        return new $className($this->entityFactory, $this->optimus, $this->connections);
    }
}
