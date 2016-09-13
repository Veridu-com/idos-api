<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\Digested;
use App\Exception\NotFound;
use Illuminate\Support\Collection;

/**
 * Database-based Digested Repository Implementation.
 */
class DBDigested extends AbstractSQLDBRepository implements DigestedInterface {
    /**
     * The table associated with the repository.
     *
     * @var string
     */
    protected $tableName = 'digested';
    /**
     * The entity associated with the repository.
     *
     * @var string
     */
    protected $entityName = 'Digested';
    /**
     * {@inheritdoc}
     */
    protected $filterableKeys = [
        'name' => 'string'
    ];

    /**
     * {@inheritdoc}
     */
    public function getAllByUserIdAndSourceId(int $userId, int $sourceId) : Collection {
        return $this->query()
            ->join('sources', 'sources.id', '=', 'digested.source_id')
            ->where('sources.user_id', '=', $userId)
            ->where('sources.id', '=', $sourceId)
            ->get(['digested.*']);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllByUserIdSourceIdAndNames(int $userId, int $sourceId, array $queryParams = []) : Collection {
        $result = $this->query()
            ->join('sources', 'sources.id', '=', 'digested.source_id')
            ->where('sources.user_id', '=', $userId)
            ->where('sources.id', '=', $sourceId);

        $result = $this->filter($result, $queryParams);

        return $result->get(['digested.*']);
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
    public function findOneByUserIdSourceIdAndName(int $userId, int $sourceId, string $name) : Digested {
        $result = new Collection();
        $result = $result->merge(
            $this->query()
                ->join('sources', 'sources.id', '=', 'digested.source_id')
                ->where('sources.user_id', '=', $userId)
                ->where('sources.id', '=', $sourceId)
                ->where('digested.name', '=', $name)
                ->get(['digested.*'])
        );

        if ($result->isEmpty()) {
            throw new NotFound();
        }

        return $result->first();
    }

    /**
     * {@inheritdoc}
     */
    public function deleteOneBySourceIdAndName(int $sourceId, string $name) : int {
        return $this->deleteBy(['source_id' => $sourceId, 'name' => $name]);
    }
}
