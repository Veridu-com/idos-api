<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\Gate;
use Illuminate\Support\Collection;

/**
 * Database-based Gate Repository Implementation.
 */
class DBGate extends AbstractDBRepository implements GateInterface {
    /**
     * The table associated with the repository.
     *
     * @var string
     */
    protected $tableName = 'gates';

    /**
     * The entity associated with the repository.
     *
     * @var string
     */
    protected $entityName = 'Gate';

    /**
     * {@inheritdoc}
     */
    protected $filterableKeys = [
        'slug'       => 'string',
        'created_at' => 'date'
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
    public function deleteByUserId(int $userId) : int {
        return $this->deleteByKey('user_id', $userId);
    }

    /**
     * {@inheritdoc}
     */
    public function findByUserId(int $userId) : Collection {
        return $this->findBy(
            [
                'user_id' => $userId,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function findByUserIdAndSlug(int $userId, string $gateSlug) : Gate {
        return $this->findOneBy(
            [
                'user_id' => $userId,
                'slug'    => $gateSlug
            ]
        );
    }
}
