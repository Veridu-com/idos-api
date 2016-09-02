<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\Source;
use Illuminate\Support\Collection;

/**
 * Database-based Source Repository Implementation.
 */
class DBSource extends AbstractDBRepository implements SourceInterface {
    /**
     * The table associated with the repository.
     *
     * @var string
     */
    protected $tableName = 'sources';
    /**
     * The entity associated with the repository.
     *
     * @var string
     */
    protected $entityName = 'Source';

    /**
     * {@inheritdoc}
     */
    public function findOne(int $id, int $userId) : Source {
        return $this->findBy(
            [
                'id'      => $id,
                'user_id' => $userId
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getAllByUserId(int $userId) : Collection {
        return $this->findBy(['user_id' => $userId]);
    }
}
