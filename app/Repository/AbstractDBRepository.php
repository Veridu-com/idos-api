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
            $this->getEntityName()
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
            return str_replace(__NAMESPACE__, '', __CLASS__);

        return $this->tableName;
    }

    /**
     * Get the entity name.
     *
     * @return string
     */
    protected function getEntityName() {
        if (empty($this->entityName))
            return str_replace(__NAMESPACE__, '\\App\\Entity\\', __CLASS__);

        return $this->entityName;
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
     * {@inheritDoc}
     */
    public function create(array $attributes) {
        return $this->entityFactory->create(
            $this->getEntityName(),
            $attributes
        );
    }

    /**
     * {@inheritDoc}
     */
    public function save(EntityInterface &$entity) {
        $id = $this->query()
            ->insertGetId($entity->serialize());
        $entity = $this->create(array_merge(['id' => $id], $entity->serialize()));
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function getAll() {
        return new Collection($this->query()->all());
    }
}
