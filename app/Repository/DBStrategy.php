<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use App\Exception\AppException;
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
    public $entityFactory;
    /**
     * SQL Database Connection.
     *
     * @var \Illuminate\Database\Connection
     */
    protected $sqlConnection;
    /**
     * NoSQL Database Connector function.
     *
     * @var callable
     */
    protected $noSqlConnector;
    /**
     * Optimus instance.
     *
     * @var \Jenssegers\Optimus\Optimus
     */
    protected $optimus;
    /**
     * Vault helper.
     *
     * @var \App\Helper\Vault
     */
    protected $vault;

    /**
     * Class constructor.
     *
     * @param \App\Factory\Entity             $entityFactory
     * @param \Jenssegers\Optimus\Optimus     $optimus
     * @param \App\Helper\Vault               $vault
     * @param \Illuminate\Database\Connection $sqlConnection
     * @param callable                        $noSqlConnector
     *
     * @return void
     */
    public function __construct(
        Entity $entityFactory,
        Optimus $optimus,
        Vault $vault,
        SQLConnection $sqlConnection,
        callable $noSqlConnector
    ) {
        $this->entityFactory  = $entityFactory;
        $this->optimus        = $optimus;
        $this->vault          = $vault;
        $this->sqlConnection  = $sqlConnection;
        $this->noSqlConnector = $noSqlConnector;
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
        static $cache = [];

        if (! isset($cache[$className])) {
            $reflectionClass   = new \ReflectionClass($className);
            $parentClass       = $reflectionClass->getParentClass()->getName();
            $cache[$className] = $parentClass;
        }

        switch ($cache[$className]) {
            case 'App\Repository\AbstractSQLDBRepository':
                return new $className(
                    $this->entityFactory,
                    $repositoryFactory,
                    $this->optimus,
                    $this->vault,
                    $this->sqlConnection
                );

            case 'App\Repository\AbstractNoSQLDBRepository':
                return new $className(
                    $this->entityFactory,
                    $repositoryFactory,
                    $this->optimus,
                    $this->vault,
                    $this->noSqlConnector
                );

            default:
                throw new AppException('Invalid repository parent class');
        }
    }
}
