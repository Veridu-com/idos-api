<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\Mapped;
use App\Exception\NotFound;
use Illuminate\Support\Collection;

/**
 * Database-based Mapped Repository Implementation.
 */
class DBMapped extends AbstractDBRepository implements MappedInterface {
    /**
     * The table associated with the repository.
     *
     * @var string
     */
    protected $tableName = 'mapped';
    /**
     * The entity associated with the repository.
     *
     * @var string
     */
    protected $entityName = 'Mapped';

    /**
     * {@inheritdoc}
     */
    public function getAllByUserIdAndSourceId(int $userId, int $sourceId) : Collection {
        $result = $this->query()
            ->join('sources', 'sources.id', '=', 'mapped.source_id')
            ->where('sources.user_id', '=', $userId)
            ->where('sources.id', '=', $sourceId)
            ->get(['mapped.*']);

        return new Collection($result);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllByUserIdSourceIdAndNames(int $userId, int $sourceId, array $names) : Collection {
        $result = $this->query()
            ->join('sources', 'sources.id', '=', 'mapped.source_id')
            ->where('sources.user_id', '=', $userId)
            ->where('sources.id', '=', $sourceId);

        if (! empty($names)) {
            $result = $result->whereIn('name', $names);
        }

        $result = $result->get(['mapped.*']);

        return new Collection($result);
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
    public function findOneByUserIdSourceIdAndName(int $userId, int $sourceId, string $name) : Mapped {
        $result = new Collection();
        $result = $result->merge(
            $this->query()
                ->join('sources', 'sources.id', '=', 'mapped.source_id')
                ->where('sources.user_id', '=', $userId)
                ->where('sources.id', '=', $sourceId)
                ->where('mapped.name', '=', $name)
                ->get(['mapped.*'])
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
