<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\Reference;
use App\Exception\NotFound;
use Illuminate\Support\Collection;

/**
 * Database-based Reference Repository Implementation.
 */
class DBReference extends AbstractSQLDBRepository implements ReferenceInterface {
    /**
     * The table associated with the repository.
     *
     * @var string
     */
    protected $tableName = 'references';
    /**
     * The entity associated with the repository.
     *
     * @var string
     */
    protected $entityName = 'Reference';
    /**
     * {@inheritdoc}
     */
    protected $filterableKeys = [
        'name'    => 'string'
    ];

    /**
     * {@inheritdoc}
     */
    public function getAllByUserId(int $userId) : Collection {
        return $this->findBy(['user_id' => $userId]);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllByUserIdAndNames(int $userId, array $queryParams = []) : Collection {
        $result = $this->query()
            ->selectRaw('"references".*')
            ->where('user_id', '=', $userId);

        $result = $this->filter($result, $queryParams);

        return $result->get();
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByUserId(int $userId) : int {
        return $this->deleteBy(['user_id' => $userId]);
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByUserIdAndName(int $userId, string $name) : Reference {
        $result = $this->findBy(['user_id' => $userId, 'name' => $name]);

        if ($result->isEmpty()) {
            throw new NotFound();
        }

        return $result->first();
    }

    /**
     * {@inheritdoc}
     */
    public function deleteOneByUserIdAndName(int $userId, string $name) : int {
        return $this->deleteBy(['user_id' => $userId, 'name' => $name]);
    }
}
