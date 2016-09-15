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
    protected $entityName = 'Gate';

    /**
     * {@inheritdoc}
     */
    protected $filterableKeys = [
        'creator.name' => 'string',
        'name' => 'string',
        'slug' => 'string',
    ];

    /**
     * {@inheritdoc}
     */
    protected $relationships = [
        'user' => [
            'type' => 'MANY_TO_ONE',
            'table' => 'users',
            'foreignKey' => 'user_id',
            'key' => 'id',
            'entity' => 'User',
            'nullable' => false,
            'hydrate' => false
        ],
        
        'creator' => [
            'type' => 'MANY_TO_ONE',
            'table' => 'services',
            'foreignKey' => 'creator',
            'key' => 'id',
            'entity' => 'Service',
            'nullable' => false,
            'hydrate' => [
                'name'
            ]
        ],
    ];

    /**
     * {@inheritdoc}
     */
    public function findByUserId(int $userId, array $queryParams = []) : Collection {
        $entities = $this->findBy([
            'user_id' => $userId
        ], $queryParams);
        
        return $entities;
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBySlug(int $userId, int $serviceId, string $slug) : Gate {
        $entity = $this->findOneBy([
            'user_id' => $userId,
            'creator' => $serviceId,
            'slug' => $slug
        ]);
        
        return $entity;
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByName(int $userId, int $serviceId, string $name) : Gate {
        $entity = $this->findOneBy([
            'user_id' => $userId,
            'creator' => $serviceId,
            'name' => $name
        ]);
        
        return $entity;
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
    public function findByUserIdAndSlug(int $userId, string $gateSlug) : Gate {
        return $this->findOneBy(
            [
                'user_id' => $userId,
                'slug'    => $gateSlug
            ]
        );
    }
}
