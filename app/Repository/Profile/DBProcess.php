<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository\Profile;

use App\Entity\Profile\Process;
use App\Exception\NotFound;
use App\Repository\AbstractSQLDBRepository;

/**
 * Database-based Process Repository Implementation.
 */
class DBProcess extends AbstractSQLDBRepository implements ProcessInterface {
    /**
     * The table associated with the repository.
     *
     * @var string
     */
    protected $tableName = 'processes';

    /**
     * The entity associated with the repository.
     *
     * @var string
     */
    protected $entityName = 'Profile\Process';

    /**
     * {@inheritdoc}
     */
    protected $filterableKeys = [
        'created_at' => 'date',
        'name'       => 'string',
        'event'      => 'string',
    ];

    /**
     * {@inheritdoc}
     */
    public function findOneBySourceId(int $sourceId) : Process {
        return $this->findOneBy(['source_id' => $sourceId]);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllByUserId(int $userId, array $queryParams = []) : array {
        $dbQuery = $this->query()->where('user_id', $userId);

        return $this->paginate(
            $this->filter($dbQuery, $queryParams),
            $queryParams
        );
    }

    /**
     * {@inheritdoc}
     */
    public function findLastByUserIdSourceIdAndEvent(int $userId, $sourceId, $event) : Process {
        $query = $this->query()
            ->where('user_id', $userId)
            ->orderBy('id', 'desc');

        if ($sourceId) {
            $query = $query->where('source_id', $sourceId);
        } else {
            $query = $query->whereNull('source_id');
        }

        if ($event) {
            $query = $query->where('event', $event);
        } else {
            $query = $query->whereNull('event');
        }

        $process = $query->first();

        if (! $process) {
            throw new NotFound();
        }

        return $process;
    }
}
