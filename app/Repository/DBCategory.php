<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use Illuminate\Support\Collection;

/**
 * Database-based Category Repository Implementation.
 */
class DBCategory extends AbstractDBRepository implements CategoryInterface {
    /**
     * The table associated with the repository.
     *
     * @var string
     */
    protected $tableName = 'categories';
    /**
     * The entity associated with the repository.
     *
     * @var string
     */
    protected $entityName = 'Category';

    /**
     * {@inheritdoc}
     */
    protected $filterableKeys = [
        'type' => 'string',
        'name' => 'string'
    ];

    /**
     * {@inheritdoc}
     */
    public function getAll(array $queryParams = []) : Collection {
        return $this->findBy([], $queryParams);
    }
}
