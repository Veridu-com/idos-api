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
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Jenssegers\Optimus\Optimus;

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
     * Filterable keys of the repository.
     *
     * @var array
     */
    protected $filterableKeys = [];

    /**
     * Begin a fluent query against a database table.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    protected function query($table = null, $entityName = null) : Builder {
        $this->dbConnection->setFetchMode(
            \PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE,
            ($entityName) ? $entityName : $this->getEntityClassName(),
            [
                [],
                $this->optimus
            ]
        );

        $table = ($table === null) ? $this->getTableName() : $table;

        return $this->dbConnection->table($table);
    }

    /**
     * Get the table name.
     *
     * @return string
     */
    protected function getTableName() : string {
        if (empty($this->tableName)) {
            throw new \RuntimeException(
                sprintf(
                    '$tableName property not set in "%s".',
                    get_class($this)
                )
            );
        }

        return $this->tableName;
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
        ConnectionInterface $dbConnection
    ) {
        parent::__construct($entityFactory, $optimus);
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
    public function save(EntityInterface $entity) : EntityInterface {
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
        return $this->deleteBy([$key => $value]);
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
            throw new \RuntimeException(
                sprintf(
                    '%s::deleteBy method was called without constraints.',
                    get_class($this)
                )
            );
        }

        $query = $this->query();
        foreach ($constraints as $key => $value) {
            $query = $query->where($key, $value);
        }

        return $query->delete();
    }

    /**
     * Update all entities that matches the given constraints.
     *
     * @param associative array $constraints ['key' => 'value']
     *
     * @return int
     */
    public function updateBy(array $constraints, array $fields) : int {
        if (! count($constraints)) {
            throw new \RuntimeException(
                sprintf(
                    '%s::updateBy method was called without constraints.',
                    get_class($this)
                )
            );
        }

        $query = $this->query();
        foreach ($constraints as $key => $value) {
            $query = $query->where($key, $value);
        }

        return $query->update($fields);
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(array $constraints, array $queryParams = []) : Collection {
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
        $query = $this->filter($this->query(), $queryParams);

        return new Collection($query->get());
    }

    /**
     * Paginates a query builder instance.
     *
     * @param \Illuminate\Database\Query\Builder $query   The query
     * @param array                              $columns The columns to retrieve
     *
     * @return array
     */
    protected function paginate(Builder $query, array $queryParams = [], array $columns = ['*']) : array {
        $page    = isset($queryParams['page']) ? $queryParams['page'] : 1;
        $perPage = isset($queryParams['perPage']) ? $queryParams['perPage'] : 15;

        $pagination = $query->paginate($perPage, $columns, 'page', $page);

        return [
            'pagination' => [
                'total'        => (int) $pagination->total(),
                'per_page'     => (int) $pagination->perPage(),
                'current_page' => (int) $pagination->currentPage(),
                'last_page'    => (int) $pagination->lastPage(),
                'from'         => (int) $pagination->firstItem(),
                'to'           => (int) $pagination->lastItem(),
            ],
            'collection' => $pagination->getCollection()
        ];

    }

    /**
     * Filters user inputs.
     *
     * @param \Illuminate\Database\Query\Builder $query       The query
     * @param array                              $queryParams The query params
     *
     * @return \Illuminate\Database\Query\Builder
     */
    protected function filter(Builder $query, array $queryParams = []) : Builder {
        $filters = [];

        foreach ($this->filterableKeys as $key => $type) {
            if (isset($queryParams[$key])) {
                $filters[$key] = [
                    'type'  => $type,
                    'value' => $queryParams[$key]
                ];
            }
        }

        if (! count($filters)) {
            return $query;
        }

        foreach ($filters as $key => $filter) {
            $value = $filter['value'];
            $type  = $filter['type'];

            switch ($type) {
                case 'date':
                    // expects query pattern to be created_at=DATE_FROM,DATE_UNTIL or created_at=EXACT_DATE
                    // expect dates to match the pattern: YYYY-MM-DD
                    $values = explode(',', $value);
                    if (count($values) == 2) {
                        $from  = $values[0];
                        $to    = $values[1];
                        $query = $query->whereDate($key, '>=', $from);
                        $query = $query->whereDate($key, '<=', $to);
                    } else {
                        // no comma
                        $query = $query->whereDate($key, '=', $value);
                    }
                    break;

                case 'string':
                    // starts or ends with "%"
                    if (preg_match('/.*%$|^%.*/', $value)) {
                        $query = $query->where($key, 'ilike', $value);
                    } else {
                        $query = $query->where($key, $value);
                    }
                    break;

                case 'boolean':
                    // avoids buggy user inputs going through the database
                    $truthyValues = [true, 1, 't', 'true', '1'];
                    if (in_array($value, $truthyValues, true)) {
                        $query = $query->where($key, '=', true);
                    } else {
                        $query = $query->where($key, '=', false);
                    }
                    break;

                default:
                    $query = $query->where($key, '=', $value);
                    break;
            }
        }

        return $query;
    }
}
