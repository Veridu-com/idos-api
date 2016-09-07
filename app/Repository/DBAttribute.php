<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\Attribute;
use App\Exception\NotFound;
use Illuminate\Support\Collection;

/**
 * Database-based Attribute Repository Implementation.
 */
class DBAttribute extends AbstractSQLDBRepository implements AttributeInterface {
    /**
     * The table associated with the repository.
     *
     * @var string
     */
    protected $tableName = 'attributes';
    /**
     * The entity associated with the repository.
     *
     * @var string
     */
    protected $entityName = 'Attribute';

    /**
     * {@inheritdoc}
     */
    public function getAllByUserId(int $userId) : Collection {
        return $this->findBy(['user_id' => $userId]);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllByUserIdAndNames(int $userId, array $names) : Collection {
        $result = $this->query()
            ->selectRaw('attributes.*')
            ->where('user_id', '=', $userId);

        if (! empty($names)) {
            $result = $result->whereIn('attributes.name', $names);
        }

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
    public function findOneByUserIdAndName(int $userId, string $name) : Attribute {
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
