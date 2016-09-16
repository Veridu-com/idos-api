<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\Normalised;
use App\Exception\NotFound;
use Illuminate\Support\Collection;

/**
 * Database-based Normalised Repository Implementation.
 */
class DBNormalised extends AbstractSQLDBRepository implements NormalisedInterface {
    /**
     * The table associated with the repository.
     *
     * @var string
     */
    protected $tableName = 'normalised';
    /**
     * The entity associated with the repository.
     *
     * @var string
     */
    protected $entityName = 'Normalised';
    /**
     * {@inheritdoc}
     */
    protected $filterableKeys = [
        'name' => 'string'
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
            'hydrate' => false
        ],
    ];

    /**
     * {@inheritdoc}
     */
    public function getAllByUserIdAndSourceId(int $userId, int $sourceId) : Collection {
        return $this->findBy([
            'source.user_id' => $userId,
            'source.id' => $sourceId
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllByUserIdSourceIdAndNames(int $userId, int $sourceId, array $queryParams = []) : Collection {
        return $this->findBy([
            'source.user_id' => $userId,
            'source.id' => $sourceId
        ], $queryParams);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteBySourceId(int $sourceId) : int {
        return $this->deleteBy(['source_id' => $sourceId]);
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByUserIdSourceIdAndName(int $userId, int $sourceId, string $name) : Normalised {
        return $this->findOneBy([
            'source.user_id' => $userId,
            'source.id' => $sourceId,
            'name' => $name
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteOneBySourceIdAndName(int $sourceId, string $name) : int {
        return $this->deleteBy(['source_id' => $sourceId, 'name' => $name]);
    }
}
