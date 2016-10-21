<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository\Profile;

use App\Entity\Profile\Attribute;
use App\Exception\Create;
use App\Exception\NotFound;
use App\Repository\AbstractSQLDBRepository;
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
    protected $entityName = 'Profile\Attribute';
    /**
     * {@inheritdoc}
     */
    protected $filterableKeys = [
        'name' => 'string'
    ];

    /**
     * {@inheritdoc}
     */
    protected $orderableKeys = [
        'name',
        'created_at',
        'updated_at'
    ];

    /**
     * {@inheritdoc}
     */
    protected $relationships = [
        'user' => [
            'type'       => 'MANY_TO_ONE',
            'table'      => 'users',
            'foreignKey' => 'user_id',
            'key'        => 'id',
            'entity'     => 'User',
            'nullable'   => false,
            'hydrate'    => false
        ]
    ];

    /**
     * {@inheritdoc}
     */
    public function upsert(int $userId, string $name, string $value) : Attribute {
        $this->beginTransaction();

        $result = $this->runRaw(
            'INSERT INTO "attributes" ("user_id", "name", "value", "created_at") VALUES (:user_id, :name, :value, :created_at)
            ON CONFLICT ("user_id", "name") DO UPDATE SET "value" = :value, "updated_at" = :updated_at',
            [
                'user_id'    => $userId,
                'name'       => $name,
                'value'      => $value,
                'created_at' => date('Y-m-d H:i:s', time()),
                'updated_at' => date('Y-m-d H:i:s', time())
            ]
        );

        if (! $result) {
            $this->rollBack();
            throw new Create\Profile\AttributeException('Error while trying to create an attribute', 500);
        }

        $this->commit();

        return $this->findOneByUserIdAndName($userId, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function findByUserId(int $userId, array $filters = []) : Collection {
        $result = $this->findBy(
            [
            'user_id' => $userId
            ], $filters
        );

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllByUserIdAndNames(int $userId, array $filters = []) : Collection {
        $result = $this->query()
            ->selectRaw('attributes.*')
            ->where('user_id', '=', $userId);

        $result = $this->filter($result, $filters);

        return $result->get();
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByUserId(int $userId, array $filters = []) : int {
        $result = $this->query()
            ->selectRaw('attributes.*')
            ->where('user_id', '=', $userId);

        if ($filters) {
            $result = $this->filter($result, $filters);
        }

        return $result->delete();
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByUserIdAndName(int $userId, string $name) : Attribute {
        $result = $this->findBy(
            [
                'user_id' => $userId,
                'name'    => $name
            ]
        );

        if ($result->isEmpty()) {
            throw new NotFound();
        }

        return $result->first();
    }

    /**
     * {@inheritdoc}
     */
    public function deleteOneByUserIdAndName(int $userId, string $name) : int {
        return $this->deleteBy(
            [
                'user_id' => $userId,
                'name'    => $name
            ]
        );
    }
}
