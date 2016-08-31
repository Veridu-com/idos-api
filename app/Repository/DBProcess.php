<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\Process;
use Illuminate\Support\Collection;

/**
 * Database-based Process Repository Implementation.
 */
class DBProcess extends AbstractDBRepository implements ProcessInterface {
    /**
     * The table associated with the repository.
     *
     * @var string
     */
    protected $tableName = 'processes';

    /**
     * The entity associated with the repository.
     *
     * @var string
     */
    protected $entityName = 'Process';

    /**
     * {@inheritdoc}
     */
    protected $filterableKeys = [
        'created_at' => 'date',
        'name' => 'string',
        'event' => 'string',
    ];


    /**
     * {@inheritdoc}
     */
    public function getAllByUserId(int $userId, array $queryParams = []) : array {
        $dbQuery = $this->query()->where('user_id', $userId);

        return $this->paginate(
            $this->filter($dbQuery, $queryParams),
            $queryParams
        );
    }

    /**
     * {@inheritdoc}
     */
    public function findWithTasks(int $id, array $queryParams = []) : Process {
        $query = $this->query()
            ->join('tasks', 'processes.id', '=', 'tasks.process_id')
            ->where('processes.id', '=', $id);

        $columns = [
            'processes.*',
            'tasks.id as tasks.id',
            'tasks.process_id as tasks.process_id',
            'tasks.name as tasks.name',
            'tasks.event as tasks.event',
            'tasks.created_at as tasks.created_a',
            'tasks.updated_at as tasks.updated_a',
            'tasks.process_id as tasks.process_id',
            'tasks.running as tasks.running',
            'tasks.success as tasks.success',
            'tasks.message as tasks.message',
        ];

        return $query->first($columns);
    }
}
