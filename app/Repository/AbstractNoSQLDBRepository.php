<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\EntityInterface;
use App\Exception\NotFound;
use App\Factory\Entity;
use App\Factory\Repository;
use Illuminate\Support\Collection;
use Jenssegers\Mongodb\Query\Builder as QueryBuilder;
use Jenssegers\Optimus\Optimus;
use MongoDB\Model\CollectionInfoIterator;
use MongoDB\Model\DatabaseInfoIterator;

/**
 * Abstract NoSQL Database-based Repository.
 */
abstract class AbstractNoSQLDBRepository extends AbstractRepository {
    /**
     * DB Collection Name.
     *
     * @var string
     */
    protected $collectionName = null;
    /**
     * Entity Name.
     *
     * @var string
     */
    protected $entityName = null;
    /**
     * NoSQL DB Connection.
     *
     * @var \Jenssegers\Mongodb\Connection
     */
    protected $dbConnection;
    /**
     * NoSQL database selector closure.
     *
     * @var callable
     */
    protected $dbSelector;

    /**
     * Filterable keys of the repository.
     *
     * @var array
     */
    protected $filterableKeys = [];

    /**
     * Select the database.
     *
     * @param string $database
     *
     * @return void
     */
    public function selectDatabase(string $database) {
        $this->dbConnection = ($this->dbSelector)($database);
    }

    /**
     * Select the collection.
     *
     * @param string $collection The collection name
     */
    public function selectCollection(string $collection) {
        $this->collectionName = $collection;
    }

    /**
     * Get selected collection name.
     *
     * @return string The collection name.
     */
    public function getCollectionName() : string {
        return $this->collectionName;
    }

    /**
     * Check if a database is selected.
     *
     * @throws \App\Exception\AppException If no database was selected
     */
    public function checkDatabaseSelected() {
        if (! $this->dbConnection) {
            throw new AppException('No NoSQL database selected');
        }
    }

    /**
     * Begins a fluent query agains a database connection.
     *
     * @param string $collection The collection name
     * @param string $entityName The entity name
     * @param string $database   The database name
     *
     * @return \Jenssegers\Mongodb\Query\Builder The query builder
     */
    protected function query(string $collection = null, string $entityName = null, string $database = null) : QueryBuilder {
        if ($database !== null) {
            $this->selectDatabase($database);
        }

        $this->checkDatabaseSelected();

        $collection = ($collection === null) ? $this->getCollectionName() : $collection;

        return $this->dbConnection->collection($collection);
    }

    /**
     * List all databases.
     *
     * @return \MongoDB\Model\DatabaseInfoIterator A iterator for the databases
     */
    protected function listDatabases() : DatabaseInfoIterator {
        return $this->dbConnection->getMongoClient()->listDatabases();
    }

    /**
     * List all collections in the selected database.
     *
     * @param string $database The database
     *
     * @return \MongoDB\Model\CollectionInfoIterator A iterator for the collections
     */
    protected function listCollections(string $database = null) : CollectionInfoIterator {
        if ($database !== null) {
            $this->selectDatabase($database);
        }

        $this->checkDatabaseSelected();

        return $this->dbConnection->getMongoDB()->listCollections();
    }

    /**
     * Drop the selected (or specified database).
     *
     * @param string $database The database
     *
     * @return mixed
     */
    protected function dropDatabase(string $database = null) {
        if ($database !== null) {
            $this->selectDatabase($database);
        }

        $this->checkDatabaseSelected();

        return $this->dbConnection->getMongoDB()->drop();
    }

    protected function dropCollection($collection = null) {
        $collection = ($collection === null) ? $this->getCollectionName() : $collection;

        return $this->dbConnection->getCollection($collection)->drop();
    }

    /**
     * Class constructor.
     *
     * @param \App\Factory\Entity                      $entityFactory
     * @param \App\Factory\Repository                  $repositoryFactory
     * @param \Jenssegers\Optimus\Optimus              $optimus
     * @param \Illuminate\Database\ConnectionInterface $dbConnection
     *
     * @return void
     */
    public function __construct(
        Entity $entityFactory,
        Repository $repositoryFactory,
        Optimus $optimus,
        callable $noSqlConnector
    ) {
        parent::__construct($entityFactory, $repositoryFactory, $optimus);

        $this->dbSelector   = $noSqlConnector;
        $this->dbConnection = null;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $attributes) : EntityInterface {
        return $this->entityFactory->create(
            $this->getEntityName(),
            $attributes
        );
    }

    /**
     * {@inheritdoc}
     */
    public function save(EntityInterface $entity) : EntityInterface {
        $this->checkDatabaseSelected();

        $serialized = $entity->serialize();

        $isUpdate = false;

        //Find if we are going to perform an update or an insert
        if ($entity->id) {
            try {
                $existingEntity = $this->find($entity->id);
                $isUpdate       = true;
            } catch (NotFound $e) {
            }
        }

        if ($isUpdate) {
            $query   = $this->query();
            $success = $query->where('_id', '=', $query->convertKey(md5((string) $entity->id)))->update($serialized) > 0;
        } else {
            if ($entity->id) {
                unset($serialized['id']);
                $entity->id = md5((string) $entity->id);
                $success    = $this->query()->insert(array_merge(['_id' => $entity->id], $serialized));
            } else {
                $entity->id = $this->query()->insertGetId($serialized);
                $success    = $entity->id !== null;
            }
        }

        return $this->create($entity->serialize());
    }

    /**
     * {@inheritdoc}
     */
    public function find(int $id) : EntityInterface {
        $this->checkDatabaseSelected();

        $result = $this->query();
        $result = $result->where('_id', '=', $result->convertKey(md5((string) $id)))->get();

        if (empty($result)) {
            throw new NotFound();
        }

        return $this->create(array_pop($result));
    }

    /**
     * {@inheritdoc}
     */
    public function delete(int $id, string $key = '_id') : int {
        $this->checkDatabaseSelected();

        $query = $this->query();

        return $query->where($key, '=', $query->convertKey(md5((string) $id)))->delete();
    }

    /**
     * Delete all entities that a key matches a value.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return int
     */
    protected function deleteByKey(string $key, $value) : int {
        $this->checkDatabaseSelected();

        return $this->deleteBy([$key => $value]);
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(array $constraints, array $queryParams = [], array $columns = []) : Collection {
        $this->checkDatabaseSelected();

        $query = $this->query();

        foreach ($constraints as $key => $value) {
            $query = $query->where($key, $value);
        }

        $query = $this->filter($query, $queryParams);

        return new Collection($query->get());
    }

    /**
     * {@inheritdoc}
     */
    public function getAll(array $queryParams = []) : Collection {
        $this->checkDatabaseSelected();

        $query = $this->filter($this->query(), $queryParams);

        return new Collection($query->get());
    }
}
