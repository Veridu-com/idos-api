<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository\Profile;

use App\Entity\Profile\Flag;
use App\Repository\AbstractSQLDBRepository;
use Illuminate\Support\Collection;

/**
 * Database-based Flag Repository Implementation.
 */
class DBFlag extends AbstractSQLDBRepository implements FlagInterface {
    /**
     * The table associated with the repository.
     *
     * @var string
     */
    protected $tableName = 'flags';
    /**
     * The entity associated with the repository.
     *
     * @var string
     */
    protected $entityName = 'Profile\Flag';
    /**
     * {@inheritdoc}
     */
    protected $filterableKeys = [
        'creator.name' => 'string',
        'slug'         => 'string'
    ];
    /**
     * {@inheritdoc}
     */
    protected $orderableKeys = [
        'slug',
        'attribute',
        'created_at',
        'updated_at'
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
            'table'      => 'services',
            'foreignKey' => 'creator',
            'key'        => 'id',
            'entity'     => 'Service',
            'nullable'   => false,
            'hydrate'    => [
                'name'
            ]
        ],
    ];

    /**
     * {@inheritdoc}
     */
    public function findOne(string $slug, int $serviceId, int $userId) : Flag {
        return $this->findOneBy(
            [
                'user_id' => $userId,
                'creator' => $serviceId,
                'slug'    => $slug
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getByUserIdAndServiceId(int $serviceId, int $userId, array $queryParams = []) : Collection {
        return $this->findBy(
            [
                'user_id' => $userId,
                'creator' => $serviceId
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
