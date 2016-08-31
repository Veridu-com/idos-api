<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\Task;
use Illuminate\Support\Collection;

/**
 * Database-based Task Repository Implementation.
 */
class DBTask extends AbstractDBRepository implements TaskInterface {
    /**
     * The table associated with the repository.
     *
     * @var string
     */
    protected $tableName = 'tasks';

    /**
     * The entity associated with the repository.
     *
     * @var string
     */
    protected $entityName = 'Task';

    /**
     * {@inheritdoc}
     */
    protected $filterableKeys = [
        'name'       => 'string',
        'event'       => 'string',
        'running'       => 'string',
        'success'       => 'string',
        'created_at' => 'date'
    ];
}
