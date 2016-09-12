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
        'source.id' => 'decoded',
        'source.name' => 'string',
        'creator' => 'string',
        'name' => 'string',
        'type' => 'string',
        'created_at' => 'date'
    ];

    /**
     * {@inheritdoc}
     */
    protected $relationships = [
        'source' => [
            'type' => 'MANY_TO_ONE',
            'table' => 'sources',
            'foreignKey' => 'source_id',
            'key' => 'id',
            'entity' => 'Source',
            'hydrate' => [
                'id',
                'name',
                'tags',
                'created_at',
                'updated_at'
            ]
        ],
    ];

    /**
     * {@inheritdoc}
     */
    public function getAllByUserId(int $userId, array $queryParams = []) : Collection {
        $result = $this->findBy([
            'user_id' => $userId
        ], $queryParams);
        
        return $result;
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
    public function findByUserIdAndSlug(int $userId, string $featureSlug) : Feature {
        return $this->findOneBy(
            [
                'user_id' => $userId,
                'slug'    => $featureSlug
            ]
        );
    }
}
