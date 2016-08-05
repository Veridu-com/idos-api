<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Repository;

use App\Entity\EntityInterface;
use App\Exception\NotFound;
use App\Factory\Entity;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Query\Builder;
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
    protected function query() : Builder {
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
    protected function getTableName() : string {
        if (empty($this->tableName))
            throw new \RuntimeException(sprintf('$tableName property not set in %s', get_class($this)));

        return $this->tableName;
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
    public function create(array $attributes) : EntityInterface {
        return $this->entityFactory->create(
            $this->getEntityName(),
            $attributes
        );
    }

    /**
     * {@inheritdoc}
     */
    public function save(EntityInterface &$entity) : EntityInterface {
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
                throw new Exception('No rows were updated when saving ' . get_class($entity));
            }
        }

        return $this->create(array_merge(['id' => $id], $entity->serialize()));
    }

    /**
     * {@inheritdoc}
     */
    public function find(int $id) : EntityInterface {
        $result = $this->query()
            ->find($id);
        if (empty($result))
            throw new NotFound();

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(int $id, string $key = 'id') : int{
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
        return $this->query()
            ->where($key, $value)
            ->delete();
    }

    /**
     * Delete all entities that matches the given constraints.
     *
     * @param associative array $constraints ['key' => 'value']
     *
     * @return int
     */
    public function deleteBy(array $constraints) : int {
        if (! count($constraints)) {
            throw new \RuntimeException(sprintf('%s@deleteBy method was called without constraints.', get_class($this)));
        }

        $query = $this->query();
        foreach ($constraints as $key => $value) {
            $query = $query->where($key, $value);
        }

        return $query->delete();
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(array $constraints) : Collection {
        $query = $this->query();
        foreach ($constraints as $key => $value) {
            $query = $query->where($key, $value);
        }

        return new Collection($query->get());
    }

    /**
     * {@inheritdoc}
     */
    public function getAll() : Collection {
        return new Collection($this->query()->all());
    }

    public function mapRelationships(Collection $items) {

        // mapear items em busca de um prefixo dentro da minha propriedade $relationships
        // para cada prefixo encontrado, colocar num array

    }
}
