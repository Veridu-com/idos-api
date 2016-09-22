<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository\Profile;

use App\Entity\Profile\Source;
use App\Repository\AbstractSQLDBRepository;
use Illuminate\Support\Collection;

/**
 * Database-based Source Repository Implementation.
 */
class DBSource extends AbstractSQLDBRepository implements SourceInterface {
    /**
     * The table associated with the repository.
     *
     * @var string
     */
    protected $tableName = 'sources';
    /**
     * The entity associated with the repository.
     *
     * @var string
     */
    protected $entityName = 'Profile\Source';

    /**
     * {@inheritdoc}
     */
    protected $filterableKeys = [
        'id'         => 'decoded',
        'name'       => 'string',
        'created_at' => 'date'
    ];

    /**
     * {@inheritdoc}
     */
    protected $orderableKeys = [
        'name',
        'created_at',
        'updated_at'
    ];

    /**
     * {@inheritdoc}
     */
    public function findOne(int $id, int $userId) : Source {
        return $this->findOneBy(
            [
                'id'      => $id,
                'user_id' => $userId
            ]
        );

        if($result->isEmpty()) {
            throw new NotFound();
        }

        return $result->first();
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByName(string $name, int $userId) : Source {
        return $this->findOneBy(
            [
                'name'    => $name,
                'user_id' => $userId
            ]
        );

        if($result->isEmpty()) {
            throw new NotFound();
        }

        return $result->first();
    }

    /**
     * {@inheritdoc}
     */
    public function getAllByUserId(int $userId) : Collection {
        return $this->findBy(['user_id' => $userId]);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByUserId(int $userId) : int {
        return $this->deleteBy(['user_id' => $userId]);
    }
}