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
     * Class constructor.
     *
     * @param \App\Factory\Entity             $entityFactory
     * @param \Jenssegers\Optimus\Optimus     $optimus
     * @param \Illuminate\Database\Connection $sqlConnection
     * @param callable                        $noSqlConnector
     *
     * @return void
     */
    public function __construct(
        Entity $entityFactory,
        Optimus $optimus,
        SQLConnection $sqlConnection,
        callable $noSqlConnector
    ) {
        $this->entityFactory  = $entityFactory;
        $this->optimus        = $optimus;
        $this->sqlConnection  = $sqlConnection;
        $this->noSqlConnector = $noSqlConnector;
    }

    /**
     * {@inheritdoc}
     */
    public function getFormattedName(string $repositoryName) : string {
        $splitName = preg_split("/\\\/", $repositoryName);

        if (is_array($splitName) && count($splitName) > 1) {
            $namespacePrefix = implode('\\', array_slice($splitName, 0, -1));
            if (substr($namespacePrefix, -1) == '\\') {
                $namespacePrefix = substr($namespacePrefix, 0, strlen($namespacePrefix) - 1);
            }

            return sprintf("%s\DB%s", $namespacePrefix, ucfirst(end($splitName)));
        }

        return sprintf('DB%s', ucfirst($repositoryName));
    }

    /**
     * {@inheritdoc}
     */
    public function build(Repository $repositoryFactory, string $className) : RepositoryInterface {
        $reflectionClass = new \ReflectionClass($className);
        $parentClass     = $reflectionClass->getParentClass()->getName();

        switch ($parentClass) {
            case 'App\Repository\AbstractSQLDBRepository':
                return new $className($this->entityFactory, $repositoryFactory, $this->optimus, $this->sqlConnection);

            case 'App\Repository\AbstractNoSQLDBRepository':
                return new $className($this->entityFactory, $repositoryFactory, $this->optimus, $this->noSqlConnector);

            default:
                throw new AppException('Invalid repository parent class');
        }
    }
}
