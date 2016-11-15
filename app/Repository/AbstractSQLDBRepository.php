<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\EntityInterface;
use App\Exception\AppException;
use App\Exception\NotFound;
use App\Factory\Entity;
use App\Factory\Repository;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Jenssegers\Optimus\Optimus;

/**
 * Abstract Database-based Repository.
 */
abstract class AbstractSQLDBRepository extends AbstractRepository {
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
     * Orderable keys of the repository.
     *
     * @var array
     */
    protected $orderableKeys = [];

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
     * {@inheritdoc}
     */
    // public function upsert(array $values, array $updates) {
    //     $columns = array_keys($values);

    //     foreach ($updates as $update) {
    //         if (is_array($update)) {
    //             $values[key($update)] = current($update);
    //         }
    //     }

    //     $sql = sprintf(
    //         'INSERT INTO "%s" (\'%s\') VALUES (:%s) ON CONFLICT DO UPDATE SET ',
    //         $this->getTableName(),
    //         implode('\', \'', $columns),
    //         implode(', :', $columns)
    //     );

    //     $this->runRaw(
    //         'INSERT INTO features (user_id, source, name, creator, type, value) VALUES (:user_id, :source, :name, :creator, :type, :value)
    //         ON CONFLICT (user_id, source, creator, name)
    //         DO UPDATE set value = :value, type = :type, updated_at = NOW()',
    //         [
    //                 'user_id' => $userId,
    //                 'source'  => $feature['source'],
    //                 'name'    => $feature['name'],
    //                 'creator' => $serviceId,
    //                 'type'    => $feature['type'],
    //                 'value'   => $feature['value']
    //             ]
    //     );

    //     $this->query()
    //         ->raw()

    // INSERT INTO distributors (did, dname)
    // VALUES (5, 'Gizmo Transglobal'), (6, 'Associated Computing, Inc')
    // ON CONFLICT (did) DO UPDATE SET dname = EXCLUDED.dname;
    // }

    /**
     * Begins a transaction.
     *
     * @throws \Exception
     *
     * @return void
     */
    public function beginTransaction() {
        return $this->dbConnection->beginTransaction();
    }

    /**
     * Commit the active database transaction.
     *
     * @throws \Exception
     *
     * @return void
     */
    public function commit() {
        return $this->dbConnection->commit();
    }

    /**
     * Rollback the active database transaction.
     *
     * @throws \Exception
     *
     * @return void
     */
    public function rollBack() {
        return $this->dbConnection->rollBack();
    }

    /**
     * Runs a raw SQL statement.
     *
     * @param string $query    The query
     * @param array  $bindings The bindings
     *
     * @throws \Illuminate\Database\QueryException
     *
     * @return bool Success of the statement
     */
    public function runRaw(string $query, array $bindings = []) : bool {
        return $this->dbConnection->statement(
            $this->dbConnection->raw($query),
            $bindings
        );
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
     * @param \App\Factory\Entity                      $entityFactory
     * @param \App\Factory\Repository                  $repositoryFactory
     * @param \Jenssegers\Optimus\Optimus              $optimus
     * @param \Illuminate\Database\ConnectionInterface $sqlConnection
     *
     * @return void
     */
    public function __construct(
        Entity $entityFactory,
        Repository $repositoryFactory,
        Optimus $optimus,
        ConnectionInterface $sqlConnection
    ) {
        parent::__construct($entityFactory, $repositoryFactory, $optimus);
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

    /**
     * Hydrates the relations of the entities. This method should be called when the provided
     * entities doesn't have the data of its relations (i.e. the provided entities were not
     * fetched in a single query with all necessary columns in the select). Generally, you would
     * call this method after creating an entity with its relations ids and saving it.
     *
     * Basically this method instantiates the relations repositories and fetches the necessary
     * data, so be aware that calling this method may generate additional queries.
     *
     * @param \App\Entity\EntityInterface|array $entities An entity or an array of entities
     *
     * @return \App\Entity\EntityInterface|array The resulting entity or array of entities
     */
    public function hydrateRelations($entities) {
        //@FIXME: this method is hydrating the relations attributes even if its not in the 'hydrate' property of the relation in the repository config, it must behave like castHydrate
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
                    // FIXME This should throw a RuntimeException if it must be implemented
                    break;
                case 'ONE_TO_MANY':
                    // FIXME This should throw a RuntimeException if it must be implemented
                    break;
                case 'MANY_TO_ONE':
                    $relationEntityName = $properties['entity'];
                    $tableForeignKey    = $properties['foreignKey'];
                    $relationTableKey   = $properties['key'];
                    $hydrateColumns     = $properties['hydrate'];

                    $relationEntity = null;
                    if ($entities->$tableForeignKey !== null && $hydrateColumns) {
                        $relationRepository = $this->repositoryFactory->create($relationEntityName);
                        $relationEntity     = $relationRepository->findOneBy(
                            [
                                $relationTableKey => $entities->$tableForeignKey
                            ],
                            [],
                            $hydrateColumns
                        );
                    }

                    $entities->relations[$relation] = $relationEntity;
                    break;
                case 'MANY_TO_MANY':
                    // FIXME This should throw a RuntimeException if it must be implemented
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

            return $this->create(array_merge(['id' => $id], $entity->serialize()));
        }

        $id = $entity->id;
        unset($serialized['id']);
        $serialized['updated_at'] = date('Y-m-d H:i:s');
        $affectedRows             = $this->query()
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
     * Upserts a register into the database.
     *
     * @param      \App\Entity\EntityInterface  $entity        The entity
     * @param      array                        $conflictKeys  The conflict keys, which keys ON CONCLIFCT will trigger.
     * @param      array                        $updateArray   The update array
     *
     * @throws     \App\Exception\NotFound      
     * @throws     \RuntimeException            
     *
     * @return     bool
     */
    public function upsert(EntityInterface $entity, array $conflictKeys = [], array $updateArray = []) : bool {
        $serialized = $entity->serialize();
        $keys = $values = [];
        $conflictSQL = '';

        foreach ($serialized as $key => $value) {
            array_push($keys, $key);
            array_push($values, $value);
        } 

        // creates conflict SQL
        if (sizeof($conflictKeys) && sizeof($updateArray)) {
            $onConflict = sprintf('(%s)', implode(',', $conflictKeys));
            $updateSql = 'DO UPDATE set ';

            // updates the updateArray
            // this has to be done because of parameter conflicting using bindParams
            $newArray = [];
            foreach ($updateArray as $key => $value) {
                $comma = last($updateArray) == $value ? '' : ',';
                $conflictKey = sprintf('conflict_%s', $key);

                $updateSql .= sprintf('%s = :%s%s', $key, $conflictKey, $comma);
                $newArray[$conflictKey] = $value;
            }
            $updateArray = $newArray;

            $conflictSQL = sprintf('%s %s', $onConflict, $updateSql);
        } else {
            $conflictSQL = 'DO NOTHING';
        }

        $insertKeys = implode(',', $keys);
        $params = array_map(function ($key) { return ":$key"; }, $keys);
        $insertParams = implode(',', $params);

        $sql = sprintf('INSERT INTO %s 
            (%s) 
            VALUES (%s)
            ON CONFLICT %s',
            $this->getTableName(),
            $insertKeys, 
            $insertParams,
            $conflictSQL
        );

        return $this->runRaw($sql, array_merge($serialized, $updateArray));
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

    /**
     * Converts the query params from the request into valid constraints to be merged in the
     * findBy method constraints.
     *
     * @param array $queryParams The query parameters
     *
     * @return array The constraints.
     */
    public function getFilterConstraints(array $queryParams) : array {
        $constraints = [];

        foreach ($queryParams as $queryParam => $value) {
            $queryParam = str_replace(':', '.', $queryParam);

            if (isset($this->filterableKeys[$queryParam])) {
                $type     = $this->filterableKeys[$queryParam];
                $operator = '=';

                switch ($type) {
                    case 'date':
                        // FIXME This should throw a RuntimeException if it must be implemented
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

                    case 'string':
                        if (strpos($value, '*') === false) {
                            break;
                        }

                        $value    = str_replace('*', '%', $value);
                        $operator = 'ILIKE';
                        break;
                }

                $constraints[$queryParam] = [$value, $operator];
            }
        }

        return $constraints;
    }

    /**
     * Gets query modifiers (limit, order, sort).
     *
     * @param \Illuminate\Database\Query\Builder $query       The query to be modified
     * @param array                              $queryParams The query parameters
     *
     * @return \Illuminate\Database\Query\Builder The modified query
     */
    public function treatQueryModifiers(Builder $query, array $queryParams) : Builder {

        if (isset($queryParams['filter:order']) && in_array($queryParams['filter:order'], $this->orderableKeys)) {
            $order = 'ASC';

            if (isset($queryParams['filter:sort']) && (in_array($queryParams['filter:sort'], ['ASC', 'DESC']))) {
                $order = $queryParams['filter:sort'];
            }

            $query = $query->orderBy($queryParams['filter:order'], $order);
        }

        if (isset($queryParams['filter:limit']) && (int) $queryParams['filter:limit'] > 0) {
            $query = $query->limit((int) $queryParams['filter:limit']);
        }

        return $query;
    }

    /**
     * Fetches all entities matching the given constraints, possibly filtered by query params
     * that comes from the user request. You can also specify which data will be fetched
     * specifying the columns array.
     *
     * The constraints array may include constraints involving the entity or its relations. It
     * is also possible to specify the desired operator for each constraint. Be aware that
     * including constraints using the entities relations may generate inner joins or left joins
     * in the generated query, although no additional queries are made.
     *
     * The query params array will be converted into valid constraints and merged into the
     * constraints array. These params may also include filters using the entity relations. Be
     * aware that including filters using the entity relations may generate additional joins in
     * the query.
     *
     * By default the fetched columns of the entity will be the ones specified in the 'public'
     * array in the entity class. Also, by default the fetched columns of the entity relations
     * are the ones specified in each relation property 'hydrate' inside the 'relationship' array
     * specified in the entity repository. All of these may be overwriten by specifying them in
     * the columns array.
     *
     * @param array $constraints The constraints
     * @param array $queryParams The query parameters
     * @param array $columns     The columns to fetch
     *
     * @return \Illuminate\Database\Collection The collection of fetched entities
     */
    public function findBy(array $constraints, array $queryParams = [], array $columns = ['*']) : Collection {
        $query = $this->query();

        $constraints = array_merge(
            $constraints,
            $this->getFilterConstraints($queryParams)
        );

        foreach ($constraints as $column => $value) {
            if (is_array($value)) {
                $query = $this->where($query, $column, $value[0], $value[1]);
                continue;
            }

            $query = $this->where($query, $column, $value);
        }

        $getColumns = [];

        foreach ($columns as $column) {
            if (strpos('.', $column) === false) {
                $getColumns[] = sprintf(
                    '%s.%s',
                    $this->getTableName(),
                    $column
                );
            }
        }

        foreach ($this->relationships as $relation => $properties) {
            if ($properties['hydrate']) {
                $query      = $this->joinWithRelation($query, $relation);
                $getColumns = array_merge($getColumns, $this->getRelationColumnsAliases($relation, $columns));
            }
        }

        $query = $this->treatQueryModifiers($query, $queryParams);

        return $this->castHydrate($query->get($getColumns));
    }

    /**
     * Inserts necessary wheres and joins in the current query. A join will be added to the query
     * if the column is from any of the entity relations.
     *
     * @param \Illuminate\Database\Query\Builder $query    The query
     * @param string                             $column   The column
     * @param mixed                              $value    The value
     * @param string                             $operator The operator
     *
     * @throws \App\Exception\AppException
     *
     * @return \Illuminate\Database\Query\Builder The query builder
     */
    protected function where($query, string $column, $value, $operator = '=') {
        $isRelationConstraint = false;
        $relationName         = null;
        $relationColumn       = null;

        if (strpos($column, '.') !== false) {
            $column               = explode('.', $column);
            $relationName         = $column[0];
            $relationColumn       = $column[1];
            $isRelationConstraint = true;
        } elseif (($relationName = $this->getRelationByForeignKey($column)) !== null) {
            $relationColumn       = $this->relationships[$relationName]['key'];
            $isRelationConstraint = true;
        }

        if (! $isRelationConstraint) {
            return $query->where($this->getTableName() . '.' . $column, $operator, $value);
        }

        if (! isset($this->relationships[$relationName])) {
            throw new AppException(
                sprintf(
                    'No relation named "%s" found for entity %s',
                    $relationName,
                    $this->entityName
                )
            );
        }

        $relationProperties = $this->relationships[$relationName];
        $relationType       = $relationProperties['type'];

        switch ($relationType) {
            case 'ONE_TO_ONE':
                $query = $this->treatOneToOneRelation(
                    $query,
                    $relationColumn,
                    $value,
                    $relationProperties,
                    $operator
                );
                break;
            case 'ONE_TO_MANY':
                $query = $this->treatOneToManyRelation(
                    $query,
                    $relationColumn,
                    $value,
                    $relationProperties,
                    $operator
                );
                break;
            case 'MANY_TO_ONE':
                $query = $this->treatManyToOneRelation(
                    $query,
                    $relationColumn,
                    $value,
                    $relationProperties,
                    $operator
                );
                break;
            case 'MANY_TO_MANY':
                $query = $this->treatManyToManyRelation(
                    $query,
                    $relationColumn,
                    $value,
                    $relationProperties,
                    $operator
                );
                break;
        }

        return $query;
    }

    /**
     * Treats a constraint for a many-to-one relation.
     *
     * @param \Illuminate\Database\Query\Builder $query              The query
     * @param string                             $relationColumn     The relation column
     * @param mixed                              $value              The value
     * @param array                              $relationProperties The relation properties (from 'relationship' array)
     * @param string                             $operator           The operator
     *
     * @return \Illuminate\Database\Query\Builder The query builder
     */
    protected function treatManyToOneRelation(
        Builder $query,
        string $relationColumn,
        $value,
        array $relationProperties,
        string $operator = '='
    ) : Builder {
        $relationTable    = $relationProperties['table'];
        $relationTableKey = $relationProperties['key'];
        $table            = $this->getTableName();
        $tableForeignKey  = $relationProperties['foreignKey'];
        $shouldHydrate    = $relationProperties['hydrate'];

        $requiresJoin     = false;
        $hasAlreadyJoined = false;
        $joinClauseKey    = null;
        if ($query->joins) {
            foreach ($query->joins as $key => $joinClause) {
                if ($joinClause->table === $relationTable) {
                    $hasAlreadyJoined = true;
                    $joinClauseKey    = $key;
                    break;
                }
            }
        }

        if ($relationColumn !== $relationTableKey || $shouldHydrate) {
            $requiresJoin = 'inner';
        }

        if ($requiresJoin && $relationColumn === $relationTableKey && ($value === 0 || $this->optimus->encode($value) === 0)) {
            $requiresJoin = 'left';
            $value        = null;
        }

        if ($hasAlreadyJoined && $query->joins[$joinClauseKey]->type !== $requiresJoin) {
            $query->joins[$joinClauseKey]->type = 'left';
        }

        if (! $hasAlreadyJoined && $requiresJoin) {
            $joinMethod = ($requiresJoin === 'left') ? 'leftJoin' : 'join';
            $query      = $query->$joinMethod($relationTable, $table . '.' . $tableForeignKey, '=', $relationTable . '.' . $relationTableKey);
        }

        if ($value === null) {
            if ($relationColumn === $relationTableKey) {
                $query->whereNull($table . '.' . $tableForeignKey);
            } else {
                $query->whereNull($relationTable . '.' . $relationColumn);
            }
        } else {
            if ($relationColumn === $relationTableKey) {
                $query->where($table . '.' . $tableForeignKey, $operator, $value);
            } else {
                $query->where($relationTable . '.' . $relationColumn, $operator, $value);
            }
        }

        return $query;
    }

    // FIXME ADD DOCUMENTATION!
    protected function treatOneToManyRelation(
        Builder $query,
        string $relationColumn,
        $value,
        array $relationProperties,
        string $operator = '='
    ) : Builder {
        $relationTable           = $relationProperties['table'];
        $relationTableForeignKey = $relationProperties['foreignKey'];
        $relationKey             = $relationProperties['key'];

        $query = $query->join($relationTable, $relationTable . '.' . $relationTableForeignKey, '=', $this->getTableName() . '.' . $relationKey);

        return $query;
    }

    /**
     * Determines if there is already a join for the given relation in the query.
     *
     * @param \Illuminate\Database\Query\Builder $query        The query
     * @param string                             $relationName The relation name
     *
     * @return bool True if there is already a join for the given relation.
     */
    protected function hasJoinWithRelation(Builder $query, string $relationName) : bool {
        $relationProperties = $this->relationships[$relationName];
        $relationTable      = $relationProperties['table'];

        if ($query->joins) {
            foreach ($query->joins as $key => $joinClause) {
                if ($joinClause->table === $relationTable) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * This method verifies if the query needs a join based on the entity relation. If any
     * additional joins are needed, they will be inserted into the query.
     *
     * This method is necessary because of the following scenario: suppose you have entity X with
     * relation 'y' (so the table of X has column 'y_id'). If I call the findBy method with a
     * constraint that is y.id = 1, no additional joins will be generated at this point, since
     * the y.id = 1 constraint can be satisfied by x.y_id = 1. Although, if you specify that
     * some columns of the relation 'y' should be fetched in the findBy, this method will detect
     * the missing join that is necessary.
     *
     * @param \Illuminate\Database\Query\Builder $query        The query
     * @param string                             $relationName The relation name
     *
     * @return \Illuminate\Database\Query\Builder The new query
     */
    protected function joinWithRelation(Builder $query, string $relationName) : Builder {
        if ($this->hasJoinWithRelation($query, $relationName)) {
            return $query;
        }

        $relationProperties = $this->relationships[$relationName];
        $relationType       = $relationProperties['type'];
        $relationTable      = $relationProperties['table'];
        $table              = $this->getTableName();

        switch ($relationType) {
            case 'ONE_TO_ONE':
                $query = $query->join();
                break;
            case 'ONE_TO_MANY':
                $relationTableForeignKey = $relationProperties['foreignKey'];
                $tableKey                = $relationProperties['key'];

                $query = $query->join($relationTable, $relationTable . '.' . $relationTableForeignKey, '=', $table . '.' . $tableKey);
                break;
            case 'MANY_TO_ONE':
                $relationTableKey = $relationProperties['key'];
                $tableForeignKey  = $relationProperties['foreignKey'];
                $nullable         = $relationProperties['nullable'];

                $joinMethod = $nullable ? 'leftJoin' : 'join';

                $query = $query->$joinMethod($relationTable, $table . '.' . $tableForeignKey, '=', $relationTable . '.' . $relationTableKey);
                break;
            case 'MANY_TO_MANY':
                $query = $query->join();
                break;
        }

        return $query;
    }

    /**
     * Returns which relation is using the given foreign key.
     *
     * @param string $foreignKeyColumn The foreign key column
     *
     * @return string|null The relation name (as in the 'relationship' array).
     */
    protected function getRelationByForeignKey(string $foreignKeyColumn) {
        foreach ($this->relationships as $relationName => $relationProperties) {
            $relationForeignKeyColumn = null;
            switch($relationProperties['type']) {
                case 'ONE_TO_ONE':
                    // FIXME This should throw a RuntimeException if it must be implemented
                    break;
                case 'ONE_TO_MANY':
                    // FIXME This should throw a RuntimeException if it must be implemented
                    break;
                case 'MANY_TO_ONE':
                    $relationForeignKeyColumn = $relationProperties['foreignKey'];
                    break;
                case 'MANY_TO_MANY':
                    // FIXME This should throw a RuntimeException if it must be implemented
                    break;
            }

            if ($relationForeignKeyColumn === $foreignKeyColumn) {
                return $relationName;
            }
        }

        return;
    }

    /**
     * Returns aliases to use in the query for the specified columns.
     *
     * @param string $relation The relation
     * @param array  $columns  The columns
     *
     * @return array The alias ready to use in the query.
     */
    public function getRelationColumnsAliases(string $relation, array $columns = ['*']) : array {
        $getColumns         = [];
        $relationProperties = $this->relationships[$relation];
        $hydrateColumns     = $relationProperties['hydrate'];
        $relationTable      = $relationProperties['table'];

        if ($columns !== ['*'] && (! isset($columns[$relation]) || empty($columns[$relation]))) {
            return [];
        }

        if ($columns === ['*'] || (isset($columns[$relation]) && empty($columns[$relation]))) {
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
            $value    = $filter['value'];
            $type     = $filter['type'];
            $column   = isset($this->keyAlias[$key]) ? $this->keyAlias[$key] : ($this->getTableName() . '.' . $key);

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
                    if (preg_match('/.*\*$|^\*.*/', $value)) {
                        $value = str_replace('*', '%', $value);
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
