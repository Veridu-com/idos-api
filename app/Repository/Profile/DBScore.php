<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository\Profile;

use App\Entity\Profile\Score;
use App\Exception\NotFound;
use Illuminate\Support\Collection;
use App\Repository\AbstractSQLDBRepository;

/**
 * Database-based Score Repository Implementation.
 */
class DBScore extends AbstractSQLDBRepository implements ScoreInterface {
    /**
     * The table associated with the repository.
     *
     * @var string
     */
    protected $tableName = 'scores';
    /**
     * The entity associated with the repository.
     *
     * @var string
     */
    protected $entityName = 'Profile\Score';
    /**
     * {@inheritdoc}
     */
    protected $filterableKeys = [
        'creator.name' => 'string',
        'attribute'    => 'string',
        'name'         => 'string'
    ];

    /**
     * {@inheritdoc}
     */
    protected $orderableKeys = [
        'attribute',
        'name',
        'created_at'
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
        ],

        'creator' => [
            'type'       => 'MANY_TO_ONE',
            'table'      => 'services',
            'foreignKey' => 'creator',
            'key'        => 'id',
            'entity'     => 'Service',
            'nullable'   => false,
            'hydrate'    => [
                'name'
            ]
        ],
    ];

    /**
     * {@inheritdoc}
     */
    public function findByUserId(int $userId, array $queryParams = []) : Collection {
        $entities = $this->findBy(
            [
            'user_id' => $userId
            ], $queryParams
        );

        return $entities;
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByName(int $userId, int $serviceId, string $name) : Score {
        $entity = $this->findOneBy(
            [
            'user_id' => $userId,
            'creator' => $serviceId,
            'name'    => $name
            ]
        );

        return $entity;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllByUserIdAttributeNameAndNames(int $userId, string $attributeName, array $queryParams = []) : Collection {
        $result = $this->query()
            ->join('attributes', 'attributes.id', '=', 'scores.attribute_id')
            ->where('attributes.user_id', '=', $userId)
            ->where('attributes.name', '=', $attributeName);

        $result = $this->filter($result, $queryParams);

        return $result->get(['scores.*']);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByAttributeId(int $attributeId) : int {
        return $this->deleteBy(['attribute_id' => $attributeId]);
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByUserIdAttributeNameAndName(int $userId, string $attributeName, string $name) : Score {
        $result = new Collection();
        $result = $result->merge(
            $this->query()
                ->join('attributes', 'attributes.id', '=', 'scores.attribute_id')
                ->where('attributes.user_id', '=', $userId)
                ->where('attributes.name', '=', $attributeName)
                ->where('scores.name', '=', $name)
                ->get(['scores.*'])
        );

        if ($result->isEmpty()) {
            throw new NotFound();
        }

        return $result->first();
    }

    /**
     * {@inheritdoc}
     */
    public function deleteOneByAttributeIdAndName(int $attributeId, string $name) : int {
        return $this->deleteBy(['attribute_id' => $attributeId, 'name' => $name]);
    }
}
