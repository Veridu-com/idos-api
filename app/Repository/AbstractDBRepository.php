<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Repository;

use App\Entity\EntityInterface;
use App\Factory\Entity;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Collection;
use App\Exception\NotFound;

/**
 * Abstract Database-based Repository.
 */
abstract class AbstractDBRepository extends AbstractRepository {
    /**
     * Entity Factory.
     *
     * @var App\Factory\Entity
     */
    protected $entityFactory;

    /**
     * DB Table Name.
     *
     * @var string
     */
    protected $tableName = null;
    /**
     * Entity Name.
     *
     * @var string
     */
    protected $entityName = null;
    /**
     * DB Connection.
     *
     * @var \Illuminate\Database\ConnectionInterface
     */
    protected $dbConnection;

    /**
     * Begin a fluent query against a database table.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    protected function query() {
        $this->dbConnection->setFetchMode(
            \PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE,
            $this->getEntityClassName()
        );
        return $this->dbConnection->table($this->getTableName());
    }

    /**
     * Get the table name.
     *
     * @return string
     */
    protected function getTableName() {
        if (empty($this->tableName))
            throw new \RuntimeException(sprintf('$tableName property not set in %s', get_class($this)));

        return $this->tableName;
    }

    /**
     * Get the entity name.
     *
     * @return string
     */
    protected function getEntityName() {
        if (empty($this->entityName))
            throw new \RuntimeException(sprintf('$entityName property not set in %s', get_class($this)));

        return $this->entityName;
    }

    /**
     * Get the entity class name.
     *
     * @return string
     */
    protected function getEntityClassName() {
        return sprintf('\\App\\Entity\\%s', $this->getEntityName());
    }

    /**
     * Class constructor.
     *
     * @param App\Factory\Entity                       $entityFactory
     * @param \Illuminate\Database\ConnectionInterface $dbConnection
     *
     * @return void
     */
    public function __construct(
        Entity $entityFactory,
        ConnectionInterface $dbConnection
    ) {
        parent::__construct($entityFactory);
        $this->dbConnection = $dbConnection;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $attributes) {
        return $this->entityFactory->create(
            $this->getEntityName(),
            $attributes
        );
    }

    /**
     * {@inheritdoc}
     */
    public function save(EntityInterface &$entity) {
        $serialized = $entity->serialize();

        if (! $entity->id) {
            $id = $this->query()->insertGetId($serialized);
        } else {
            $id = $entity->id;
            unset($serialized['id']);
            $affectedRows = $this->query()->where('id', $entity->id)->update($serialized);
            if (! $affectedRows) {
                throw new Exception("No rows were updated when saving " . get_class($entity));
            }
        }

        return $this->create(array_merge(['id' => $id], $entity->serialize()));
    }

    /**
     * {@inheritdoc}
     */
    public function find($id) {
        $result = $this->query()
            ->find($id);
        if (empty($result))
            throw new NotFound();

        return $result;
    }

    /**
     * Find the first entity that a key matches value.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @throws App\Exception\NotFound
     *
     * @return App\Entity\EntityInterface
     */
    protected function findByKey($key, $value) {
        $result = $this->query()
            ->where($key, $value)
            ->first();
        if (empty($result))
            throw new NotFound();

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($id) {
        return $this->query()
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
    protected function deleteByKey($key, $value) {
        return $this->query()
            ->where($key, $value)
            ->delete();
    }

    /**
     * Return an entity collection with all entities that a key matches a value.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getAllByKey($key, $value) {
        return new Collection(
            $this->query()
                ->where($key, $value)
                ->get()
        );
    }

    /**
     * Return an entity collection with all entities that has where constraints (AND)
     *
     * @param array $wheres
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getAllByWhereConstraints(array $wheres = []) {
        $qb = $this->query();
        foreach ($wheres as $key => $value) {
            $qb = $qb->where($key, $value);
        }
        return new Collection($qb->get());
    }

    /**
     * {@inheritdoc}
     */
    public function getAll() {
        return new Collection($this->query()->all());
    }
}
