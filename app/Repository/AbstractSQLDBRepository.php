<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\EntityInterface;
use App\Exception\NotFound;
use App\Exception\AppException;
use App\Factory\Entity;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Jenssegers\Optimus\Optimus;

/**
 * Abstract Database-based Repository.
 */
abstract class AbstractSQLDBRepository extends AbstractRepository {
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
        if ($entityName === null) {
            $entityName = $this->getEntityClassName();
        }

        $this->dbConnection->setFetchMode(
            \PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE,
            $entityName,
            [
                [],
                $this->optimus
            ]
        );

        if ($table === null) {
            $table = $this->getTableName();
        }

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
     * @param \Illuminate\Database\ConnectionInterface $sqlConnection
     *
     * @return void
     */
    public function __construct(
        Entity $entityFactory,
        Optimus $optimus,
        ConnectionInterface $sqlConnection
    ) {
        parent::__construct($entityFactory, $optimus);
        $this->dbConnection = $sqlConnection;
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

    public function hydrateRelations($entities) {
        if (is_array($entities)) {
            foreach ($entities as $key => $entity) {
                $entities[$key] = $this->hydrateRelations($entity);
            }

            return $entities;
        }

        foreach ($this->relationships as $relation => $properties) {
            if (! $properties['hydrate']) {
                continue;
            }

            switch ($properties['type']) {
                case 'ONE_TO_ONE':
                
                    break;
                case 'ONE_TO_MANY':
                    
                    break;
                case 'MANY_TO_ONE':
                    $relationEntity = $this->    
                    break;
                case 'MANY_TO_MANY':
                    
                    break;
            }
        }

        return $entities;
    }

    /**
     * {@inheritdoc}
     */
    public function save(EntityInterface $entity) : EntityInterface {
        $serialized = $entity->serialize();

        if (! $entity->id) {
            $id = $this->query()->insertGetId($serialized);
            $entity = $this->hydrateRelations($entity);

            return $this->create(array_merge(['id' => $id], $entity->serialize()));
        }

        $id = $entity->id;
        unset($serialized['id']);
        $serialized['updated_at'] = date('Y-m-d H:i:s');
        
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
    public function delete(int $id, string $key = 'id') : int {
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

    protected function resolveConstraintValue($value) {
        if (is_array($value)) {
            return $value[0] . ' ' . $value[1];
        }

        return $value;
    }

    protected function hasJoinWithRelation($query, $relationName) {
        $relationProperties = $this->relationships[$relationName];
        $relationTable = $relationProperties['table'];

        if ($query->joins) {
            foreach ($query->joins as $key => $joinClause) {
                if ($joinClause->table === $relationTable) {
                    return true;
                }
            }
        }

        return false;
    }

    protected function joinWithRelation($query, $relationName) {
        if ($this->hasJoinWithRelation($query, $relationName)) {
            return $query;
        }

        $relationProperties = $this->relationships[$relationName];
        $relationType = $relationProperties['type'];
        $relationTable = $relationProperties['table'];
        $table = $this->getTableName();

        switch ($relationType) {
            case 'ONE_TO_ONE':
                $query = $query->join();
                break;
            case 'ONE_TO_MANY':
                $relationTableForeignKey = $relationProperties['foreignKey'];
                $tableKey = $relationProperties['key'];

                $query = $query->join($relationTable, $relationTable . '.' . $relationTableForeignKey, '=', $table . '.' . $tableKey);
                break;
            case 'MANY_TO_ONE':
                $relationTableKey = $relationProperties['key'];
                $tableForeignKey = $relationProperties['foreignKey'];

                $query = $query->join($relationTable, $table . '.' . $tableForeignKey, '=', $relationTable . '.' . $relationTableKey);
                break;
            case 'MANY_TO_MANY':
                $query = $query->join();
                break;
        }

        return $query;
    }

    protected function treatOneToManyRelation($query, $relationColumn, $value, $relationProperties) {
        $relationTable = $relationProperties['table'];
        $relationTableForeignKey = $relationProperties['foreignKey'];
        $relationKey = $relationProperties['key'];

        $query = $query->join($relationTable, $relationTable . '.' . $relationTableForeignKey, '=', $this->getTableName() . '.' . $relationKey);

        return $query;
    }

    protected function treatManyToOneRelation($query, $relationColumn, $value, $relationProperties) {
        $relationTable = $relationProperties['table'];
        $relationTableKey = $relationProperties['key'];
        $table = $this->getTableName();
        $tableForeignKey = $relationProperties['foreignKey'];

        $hasAlreadyJoined = false;
        $joinClauseKey = null;
        if ($query->joins) {
            foreach ($query->joins as $key => $joinClause) {
                if ($joinClause->table === $relationTable) {
                    $hasAlreadyJoined = true;
                    $joinClauseKey = $key;
                    break;
                }
            }
        }

        $requiresLeftJoin = false;
        if ($relationColumn === $relationTableKey && ($value === 0 || $this->optimus->encode($value) === 0)) {
            $requiresLeftJoin = true;
            $value = null;
        }

        if ($hasAlreadyJoined && $requiresLeftJoin && $query->joins[$joinClauseKey]->type !== 'left') {
            $query->joins[$joinClauseKey]->type = 'left';
        }

        if (! $hasAlreadyJoined) {
            $joinMethod = $requiresLeftJoin ? 'leftJoin' : 'join';
            $query = $query->$joinMethod($relationTable, $table . '.' . $tableForeignKey, '=', $relationTable . '.' . $relationTableKey);
        }

        if ($value === null) {
            if ($relationColumn === $relationTableKey) {
                $query->whereNull($table . '.' . $tableForeignKey);
            } else {
                $query->whereNull($relationTable . '.' . $relationColumn);
            }
        } else {
            $query->where($relationTable . '.' . $relationColumn, '=', $value);
        }

        return $query;
    }

    protected function where($query, $column, $value) {
        $isRelationConstraint = (strpos($column, '.') !== false);

        if (! $isRelationConstraint) {
            return $query->where($this->getTableName() . '.' . $column, $value);
        }

        $column = explode('.', $column);
        $relationName = $column[0];
        $relationColumn = $column[1];

        if (! isset($this->relationships[$relationName])) {
            throw new AppException('No relation named "' . $relationName . '" found for entity ' . $this->entityName);
        }

        $relationProperties = $this->relationships[$relationName];
        $relationType = $relationProperties['type'];

        switch ($relationType) {
            case 'ONE_TO_ONE':
                $query = $this->treatOneToOneRelation($query, $relationColumn, $value, $relationProperties);
                break;
            case 'ONE_TO_MANY':
                $query = $this->treatOneToManyRelation($query, $relationColumn, $value, $relationProperties);
                break;
            case 'MANY_TO_ONE':
                $query = $this->treatManyToOneRelation($query, $relationColumn, $value, $relationProperties);
                break;
            case 'MANY_TO_MANY':
                $query = $this->treatManyToManyRelation($query, $relationColumn, $value, $relationProperties);
                break;
        }

        return $query;
    }

    public function findBy(array $constraints, array $queryParams = [], array $columns = []) : Collection {
        $query = $this->query();

        $constraints = array_merge($constraints, $this->getFilterConstraints($queryParams));

        foreach ($constraints as $column => $value) {
            $query = $this->where($query, $column, $value);
        }

        $getColumns = [$this->getTableName() . '.*'];

        foreach ($this->relationships as $relation => $properties) {
            if ($properties['hydrate']) {
                $query = $this->joinWithRelation($query, $relation);
                $getColumns = array_merge($getColumns, $this->getRelationColumnsAliases($relation, $columns));
            }
        }

        return $this->castHydrate($query->get($getColumns));
    }

    public function getRelationColumnsAliases($relation, array $columns = []) {
        $getColumns = [];
        $relationProperties = $this->relationships[$relation];
        $hydrateColumns = $relationProperties['hydrate'];
        $relationTable = $relationProperties['table'];

        if (! empty($columns) && (! isset($columns[$relation]) || empty($columns[$relation]))) {
            return [];
        }

        if (empty($columns) || (isset($columns[$relation]) && empty($columns[$relation]))) {
            $columns[$relation] = $hydrateColumns;

            if (! $hydrateColumns) {
                return [];
            }
        }

        foreach ($columns[$relation] as $column) {
            $getColumns[] = $relationTable . '.' . $column . ' as ' . $relation . '.' . $column;
        }

        return $getColumns;
    }

    public function getFilterConstraints(array $queryParams) {
        $constraints = [];

        foreach ($queryParams as $queryParam => $value) {
            $queryParam = str_replace(':', '.', $queryParam);

            if (isset($this->filterableKeys[$queryParam])) {
                $type = $this->filterableKeys[$queryParam];

                switch ($type) {
                    case 'date':

                        break;

                    case 'boolean':
                        $value = (bool) $value;
                        break;

                    case 'integer':
                        $value = (int) $value;
                        break;

                    case 'decoded':
                        $value = $this->optimus->decode((int) $value);
                        break;

                    default:
                }

                $constraints[$queryParam] = $value;
            }
        }

        return $constraints;
    }

    /**
     * {@inheritdoc}
     */
    public function getAll(array $queryParams = []) : Collection {
        $query = $this->filter($this->query(), $queryParams);

        return $query->get();
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
            $keyParts = explode(':', $key);
            $value  = $filter['value'];
            $type   = $filter['type'];
            $column = isset($this->keyAlias[$key]) ? $this->keyAlias[$key] : ($this->getTableName() . '.' . $key);

            if (count($keyParts) == 2 && $keyParts[1] === 'id' && (int) $value === 0) {
                return $query->whereNull($column);
            }

            switch ($type) {
                case 'date':
                    // expects query pattern to be created_at=DATE_FROM,DATE_UNTIL or created_at=EXACT_DATE
                    // expect dates to match the pattern: YYYY-MM-DD
                    $values = explode(',', $value);
                    if (count($values) == 2) {
                        $from  = $values[0];
                        $to    = $values[1];
                        $query = $query->whereDate($column, '>=', $from);
                        $query = $query->whereDate($column, '<=', $to);
                    } else {
                        // no comma
                        $query = $query->whereDate($column, '=', $value);
                    }
                    break;

                case 'string':
                    // starts or ends with "%"
                    if (preg_match('/.*%$|^%.*/', $value)) {
                        $query = $query->where($column, 'ilike', $value);
                    } else {
                        $query = $query->where($column, $value);
                    }
                    break;

                case 'boolean':
                    // avoids buggy user inputs going through the database
                    $truthyValues = [true, 1, 't', 'true', '1'];
                    if (in_array($value, $truthyValues, true)) {
                        $query = $query->where($column, '=', true);
                    } else {
                        $query = $query->where($column, '=', false);
                    }
                    break;

                case 'decoded':
                    $query = $query->where($column, '=', $this->optimus->decode($value));
                    break;

                default:
                    $query = $query->where($column, '=', $value);
                    break;
            }
        }

        return $query;
    }
}
