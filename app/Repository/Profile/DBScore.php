<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository\Profile;

use App\Entity\Profile\Score;
use App\Repository\AbstractDBRepository;
use Illuminate\Support\Collection;

/**
 * Database-based Score Repository Implementation.
 */
class DBScore extends AbstractDBRepository implements ScoreInterface {
    /**
     * The table associated with the repository.
     *
     * @var string
     */
    protected $tableName = 'scores';
    /**
     * The entity associated with the repository.
     *
     * @var string
     */
    protected $entityName = 'Profile\Score';
    /**
     * {@inheritdoc}
     */
    protected $filterableKeys = [
        'creator.name' => 'string',
        'attribute'    => 'string',
        'name'         => 'string'
    ];
    /**
     * {@inheritdoc}
     */
    protected $orderableKeys = [
        'attribute',
        'name',
        'created_at'
    ];
    /**
     * {@inheritdoc}
     */
    protected $relationships = [
        'user' => [
            'type'       => 'MANY_TO_ONE',
            'table'      => 'users',
            'foreignKey' => 'user_id',
            'key'        => 'id',
            'entity'     => 'User',
            'nullable'   => false,
            'hydrate'    => false
        ],

        'creator' => [
            'type'       => 'MANY_TO_ONE',
            'table'      => 'handlers',
            'foreignKey' => 'creator',
            'key'        => 'id',
            'entity'     => 'Handler',
            'nullable'   => false,
            'hydrate'    => [
                'name'
            ]
        ],
    ];

    /**
     * {@inheritdoc}
     */
    public function findOne(string $name, int $handlerId, int $userId) : Score {
        return $this->findOneBy(
            [
                'user_id' => $userId,
                'creator' => $handlerId,
                'name'    => $name
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getByUserIdAndHandlerId(int $handlerId, int $userId, array $queryParams = []) : Collection {
        return $this->findBy(
            [
                'user_id' => $userId,
                'creator' => $handlerId
            ], $queryParams
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getByUserId(int $userId, array $queryParams = []) : Collection {
        return $this->findBy(
            [
            'user_id' => $userId
            ], $queryParams
        );
    }
}
