<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository\Profile;

use App\Entity\Profile\Gate;
use App\Repository\AbstractSQLDBRepository;
use Illuminate\Support\Collection;

/**
 * Database-based Gate Repository Implementation.
 */
class DBGate extends AbstractSQLDBRepository implements GateInterface {
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
    protected $entityName = 'Profile\Gate';
    /**
     * {@inheritdoc}
     */
    protected $filterableKeys = [
        'creator.name' => 'string',
        'name'         => 'string',
        'slug'         => 'string',
    ];
    /**
     * {@inheritdoc}
     */
    protected $orderableKeys = [
        'name',
        'slug',
        'pass',
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
    public function findOne(string $slug, int $serviceId, int $userId) : Gate {
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
    public function findOneBySlug(string $slug, int $serviceId, int $userId) : Gate {
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
    public function getByServiceIdAndUserId(int $serviceId, int $userId, array $queryParams = []) : Collection {
        return $this->findBy(
            [
                'creator' => $serviceId,
                'user_id' => $userId
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

    /**
     * {@inheritdoc}
     */
    public function deleteByUserId(int $userId) : int {
        return $this->deleteByKey('user_id', $userId);
    }
}
