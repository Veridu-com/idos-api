<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

/**
 * Database-based Process Repository Implementation.
 */
class DBProcess extends AbstractSQLDBRepository implements ProcessInterface {
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
        'name'       => 'string',
        'event'      => 'string',
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
}
