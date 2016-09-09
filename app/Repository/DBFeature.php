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
        'source:id' => 'decoded',
        'source:name' => 'string',
        'creator' => 'string',
        'name' => 'string',
        'type' => 'string',
        'created_at' => 'date'
    ];

    /**
     * {@inheritdoc}
     */
    protected $keyAlias = [
        'source:id' => 'source_id',
        'source:name' => 'sources.name'
    ];

    /**
     * {@inheritdoc}
     */
    public function getAllByUserId(int $userId, array $queryParams = []) : Collection {
        $dbQuery = $this->query();

        if (! isset($queryParams['source:id']) || (int) $queryParams['source:id'] !== 0) {
            $dbQuery = $dbQuery->leftjoin('sources', 'sources.id', 'features.source_id')->where('features.user_id', $userId);
            $result = $this->filter($dbQuery, $queryParams)->get(['features.*', 'sources.id as source.id', 'sources.name as source.name', 'sources.tags as source.tags', 'sources.created_at as source.created_at', 'sources.updated_at as source.created_at']);            
        } else {
            $dbQuery = $dbQuery->where('features.user_id', $userId);
            return $this->filter($dbQuery, $queryParams)->get(['features.*']);
        } 

        return $this->castHydrate($result);
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
