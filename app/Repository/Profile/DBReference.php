<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository\Profile;

use App\Entity\Profile\Reference;
use App\Repository\AbstractSQLDBRepository;
use Illuminate\Support\Collection;

/**
 * Database-based Reference Repository Implementation.
 */
class DBReference extends AbstractSQLDBRepository implements ReferenceInterface {
    /**
     * The table associated with the repository.
     *
     * @var string
     */
    protected $tableName = 'references';
    /**
     * The entity associated with the repository.
     *
     * @var string
     */
    protected $entityName = 'Profile\Reference';
    /**
     * {@inheritdoc}
     */
    protected $filterableKeys = [
        'name' => 'string'
    ];

    /**
     * {@inheritdoc}
     */
    public function findOne(string $name, int $userId) : Reference {
        return $this->findOneBy(
            [
            'user_id' => $userId,
            'name'    => $name
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getAllByUserId(int $userId, array $queryParams = []) : Collection {
        return $this->findBy(['user_id' => $userId], $queryParams);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteOne(string $name, int $userId) : int {
        return $this->deleteBy(['user_id' => $userId, 'name' => $name]);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByUserId(int $userId) : int {
        return $this->deleteBy(['user_id' => $userId]);
    }
}
