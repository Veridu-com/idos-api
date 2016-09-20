<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\Feature;
use Illuminate\Support\Collection;

/**
 * Database-based Feature Repository Implementation.
 */
class DBFeature extends AbstractSQLDBRepository implements FeatureInterface {
    /**
     * The table associated with the repository.
     *
     * @var string
     */
    protected $tableName = 'features';

    /**
     * The entity associated with the repository.
     *
     * @var string
     */
    protected $entityName = 'Feature';

    /**
     * {@inheritdoc}
     */
    protected $filterableKeys = [
        'creator.name' => 'string',
        'source'       => 'string',
        'name'         => 'string',
        'type'         => 'string',
        'created_at'   => 'date'
    ];

    /**
     * {@inheritdoc}
     */
    protected $orderableKeys = [
        'source',
        'name',
        'type',
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
    public function findByUserId(int $userId, array $queryParams = []) : Collection {
        $result = $this->findBy(
            [
            'user_id' => $userId
            ], $queryParams
        );

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByUserId(int $userId, array $queryParams = []) : int {
        return $this->deleteByKey('user_id', $userId);
    }

    /**
     * {@inheritdoc}
     */
    public function findOneById(int $userId, string $sourceName, int $serviceId, int $id) : Feature {
        return $this->findOneBy(
            [
            'user_id' => $userId,
            'source'  => $sourceName,
            'creator' => $serviceId,
            'id'      => $id
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByName(int $userId, string $sourceName, int $serviceId, string $name) : Feature {
        return $this->findOneBy(
            [
            'user_id' => $userId,
            'source'  => $sourceName,
            'creator' => $serviceId,
            'name'    => $name
            ]
        );
    }
}
