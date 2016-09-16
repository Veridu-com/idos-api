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
        'source.id'    => 'decoded',
        'source.name'  => 'string',
        'creator.name' => 'string',
        'name'         => 'string',
        'type'         => 'string',
        'created_at'   => 'date'
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

        'source' => [
            'type'       => 'MANY_TO_ONE',
            'table'      => 'sources',
            'foreignKey' => 'source_id',
            'key'        => 'id',
            'entity'     => 'Source',
            'nullable'   => true,
            'hydrate'    => [
                'id',
                'user_id',
                'name',
                'tags',
                'created_at',
                'updated_at'
            ]
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
    public function findOneById(int $userId, int $sourceId, int $serviceId, int $id) : Feature {
        return $this->findOneBy(
            [
            'user_id'   => $userId,
            'source_id' => $sourceId,
            'creator'   => $serviceId,
            'id'        => $id
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByName(int $userId, int $sourceId, int $serviceId, string $name) : Feature {
        return $this->findOneBy(
            [
            'user_id'   => $userId,
            'source_id' => $sourceId,
            'creator'   => $serviceId,
            'name'      => $name
            ]
        );
    }
}
