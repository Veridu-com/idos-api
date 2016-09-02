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
class DBNormalised extends AbstractDBRepository implements NormalisedInterface {
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
    public function getAllByUserIdAndSourceId(int $userId, int $sourceId) : Collection {
        return $this->query()
            ->join('sources', 'sources.id', '=', 'normalised.source_id')
            ->where('sources.user_id', '=', $userId)
            ->where('sources.id', '=', $sourceId)
            ->get(['normalised.*']);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllByUserIdSourceIdAndNames(int $userId, int $sourceId, array $names) : Collection {
        $result = $this->query()
            ->join('sources', 'sources.id', '=', 'normalised.source_id')
            ->where('sources.user_id', '=', $userId)
            ->where('sources.id', '=', $sourceId);

        if (! empty($names)) {
            $result = $result->whereIn('normalised.name', $names);
        }

        return $result->get(['normalised.*']);
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
        $result = new Collection();
        $result = $result->merge(
            $this->query()
                ->join('sources', 'sources.id', '=', 'normalised.source_id')
                ->where('sources.user_id', '=', $userId)
                ->where('sources.id', '=', $sourceId)
                ->where('normalised.name', '=', $name)
                ->get(['normalised.*'])
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
