<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository\Profile;

use App\Entity\Profile\Candidate;
use App\Exception\NotFound;
use App\Repository\AbstractSQLDBRepository;
use Illuminate\Support\Collection;

/**
 * Database-based Candidate Repository Implementation.
 */
class DBCandidate extends AbstractSQLDBRepository implements CandidateInterface {
    /**
     * The table associated with the repository.
     *
     * @var string
     */
    protected $tableName = 'candidates';
    /**
     * The entity associated with the repository.
     *
     * @var string
     */
    protected $entityName = 'Profile\Candidate';
    /**
     * {@inheritdoc}
     */
    protected $filterableKeys = [
        'creator.name' => 'string',
        'attribute'    => 'string'
    ];

    /**
     * {@inheritdoc}
     */
    protected $orderableKeys = [
        'attribute',
        'support',
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
            ->selectRaw('candidates.*')
            ->where('user_id', '=', $userId);

        $result = $this->filter($result, $filters);

        return $result->get();
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByUserId(int $userId, array $filters = []) : int {
        $result = $this->query()
            ->selectRaw('candidates.*')
            ->where('user_id', '=', $userId);

        if ($filters) {
            $result = $this->filter($result, $filters);
        }

        return $result->delete();
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByUserIdAndAttributeName(int $userId, string $attributeName) : Candidate {
        $result = $this->findBy(['user_id' => $userId, 'attribute' => $attributeName]);

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
