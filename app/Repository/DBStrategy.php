<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use App\Factory\Entity;
use App\Factory\Repository;
use App\Helper\Vault;
use Illuminate\Database\Connection as SQLConnection;
use Jenssegers\Optimus\Optimus;

/**
 * Database-based Repository Strategy.
 */
class DBStrategy implements RepositoryStrategyInterface {
    /**
     * Entity Factory.
     *
     * @var \App\Factory\Entity
     */
    private $entityFactory;
    /**
     * SQL Database Connection.
     *
     * @var \Illuminate\Database\Connection
     */
    private $sqlConnection;
    /**
     * Optimus instance.
     *
     * @var \Jenssegers\Optimus\Optimus
     */
    private $optimus;
    /**
     * Vault helper.
     *
     * @var \App\Helper\Vault
     */
    private $vault;

    /**
     * Class constructor.
     *
     * @param \App\Factory\Entity             $entityFactory
     * @param \Jenssegers\Optimus\Optimus     $optimus
     * @param \App\Helper\Vault               $vault
     * @param \Illuminate\Database\Connection $sqlConnection
     *
     * @return void
     */
    public function __construct(
        Entity $entityFactory,
        Optimus $optimus,
        Vault $vault,
        SQLConnection $sqlConnection
    ) {
        $this->entityFactory  = $entityFactory;
        $this->optimus        = $optimus;
        $this->vault          = $vault;
        $this->sqlConnection  = $sqlConnection;
    }

    /**
     * {@inheritdoc}
     */
    public function getFormattedName(string $repositoryName) : string {
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
        return new $className(
            $this->entityFactory,
            $repositoryFactory,
            $this->optimus,
            $this->vault,
            $this->sqlConnection
        );
    }
}
