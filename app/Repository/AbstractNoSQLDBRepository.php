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
use Illuminate\Support\Collection;
use Jenssegers\Optimus\Optimus;
use Jenssegers\Mongodb\Connection as MongoDbConnection;
use Jenssegers\Mongodb\Query\Builder as QueryBuilder;

/**
 * Abstract NoSQL Database-based Repository.
 */
abstract class AbstractNoSQLDBRepository extends AbstractRepository {
    /**
     * Entity Factory.
     *
     * @var App\Factory\Entity
     */
    protected $entityFactory;

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
     * @var mixed
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
     * Check if a database is selected.
     */
    public function checkDatabaseSelected() {
        if (! $this->dbConnection) {
            throw new AppException('No NoSQL database selected');
        }
    }

    /**
     * Begin a fluent query against a database collection.
     *
     * @return \Jenssegers\Mongodb\Query\Builder
     */
    protected function query($collection = null, $entityName = null, $database = null) : Builder {
        if ($database !== null) {
            $this->selectDatabase($database);
        }

        $this->checkDatabaseSelected();

        $collection = ($collection === null) ? $this->getCollectionName() : $collection;

        return $this->dbConnection->collection($collection);
    }

    /**
     * Class constructor.
     *
     * @param App\Factory\Entity                       $entityFactory
     * @param \Jenssegers\Optimus\Optimus              $optimus
     * @param \Illuminate\Database\ConnectionInterface $dbConnection
     *
     * @return void
     */
    public function __construct(
        Entity $entityFactory,
        Optimus $optimus,
        array $connections
    ) {
        parent::__construct($entityFactory, $optimus);

        $this->dbSelector = $connections['nosql'];
        $this->dbConnection = null;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $attributes) : EntityInterface {
        $this->checkDatabaseSelected();

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

        if (! $entity->id) {
            $id = $this->query()
                ->insertGetId($serialized);
        } else {
            $id = $entity->id;
            unset($serialized['id']);
            $affectedRows = $this->query()
                ->where('id', $entity->id)
                ->update($serialized);
            if (! $affectedRows) {
                throw new \RuntimeException(
                    sprintf(
                        'No rows were updated when saving "%s".',
                        get_class($entity)
                    )
                );
            }
        }

        return $this->create(array_merge(['id' => $id], $entity->serialize()));
    }

    /**
     * {@inheritdoc}
     */
    public function find(int $id) : EntityInterface {
        $this->checkDatabaseSelected();

        $result = $this->query()
            ->find($id);
        if (empty($result)) {
            throw new NotFound();
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(int $id, string $key = 'id') : int {
        $this->checkDatabaseSelected();

        return $this->query()
            ->where($key, $id)
            ->delete($id);
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
    public function findBy(array $constraints, array $queryParams = []) : Collection {
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
