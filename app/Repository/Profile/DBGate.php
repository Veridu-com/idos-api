<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository\Profile;

use App\Entity\Profile\Gate;
use App\Repository\AbstractDBRepository;
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
            'table'      => 'handlers',
            'foreignKey' => 'creator',
            'key'        => 'id',
            'entity'     => 'Handler',
            'nullable'   => false,
            'hydrate'    => [
                'name'
            ]
        ],

        'category' => [
            'type'       => 'MANY_TO_ONE',
            'table'      => 'categories',
            'foreignKey' => 'name',
            'key'        => 'name',
            'entity'     => 'Category',
            'nullable'   => false,
            'hydrate'    => [
                'display_name',
                'description'
            ]
        ],
    ];

    /**
     * {@inheritdoc}
     */
    public function findBySlug(string $slug, int $handlerId, int $userId) : Gate {
        return $this->findOneBy(
            [
                'user_id' => $userId,
                'creator' => $handlerId,
                'slug'    => $slug
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function findByName(string $name, int $handlerId, int $userId) : Gate {
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
    public function getByHandlerIdAndUserId(int $handlerId, int $userId, array $queryParams = []) : Collection {
        return $this->findBy(
            [
                'creator' => $handlerId,
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
