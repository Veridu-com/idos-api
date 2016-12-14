<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository\Profile;

use App\Entity\Profile\Gate;
use App\Repository\AbstractSQLDBRepository;
use Illuminate\Support\Collection;

/**
 * Database-based Gate Repository Implementation.
 */
class DBGate extends AbstractSQLDBRepository implements GateInterface {
    /**
     * The table associated with the repository.
     *
     * @var string
     */
    protected $tableName = 'gates';

    /**
     * The entity associated with the repository.
     *
     * @var string
     */
    protected $entityName = 'Profile\Gate';
    /**
     * {@inheritdoc}
     */
    protected $filterableKeys = [
        'creator.name' => 'string',
        'name'         => 'string',
        'slug'         => 'string',
    ];
    /**
     * {@inheritdoc}
     */
    protected $orderableKeys = [
        'name',
        'slug',
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
     * Returns the category associated with a gate.
     *
     * @param \App\Entity\Profile\Gate $gate The gate entity
     *
     * @return \App\Entity\Profile\Gate
     */
    public function findCategory(Gate $gate) {
        $category = $this
            ->repositoryFactory
            ->create('Category')
            ->getAll([
                'type' => 'gate',
                'name' => $gate->name
            ]);

        if (count($category) > 0) {
            return $category->first();
        }

        return null;
    }

    /**
     * Associate the categories with the given gates.
     *
     * @param \Illuminate\Support\Collection $gates The gates entities
     *
     * @return \Illuminate\Support\Collection
     */
    public function associateCategories(Collection $gates) {
        $categories = $this
            ->repositoryFactory
            ->create('Category')
            ->getAll([
                'type' => 'gate'
            ]);

        $gateCategories = [];
        foreach ($categories as $category) {
            $gateCategories[$category->name] = $category->toArray();
        }

        foreach ($gates as $gate) {
            $gate->category = isset($gateCategories[$gate->name]) ? $gateCategories[$gate->name] : null;
        }

        return $gates;
    }

    /**
     * {@inheritdoc}
     */
    public function findBySlug(string $slug, int $serviceId, int $userId) : Gate {
        $gate = $this->findOneBy(
            [
                'user_id' => $userId,
                'creator' => $serviceId,
                'slug'    => $slug
            ]
        );

        $category = $this->findCategory($gate);
        $gate->category = $category ? $category->toArray() : null;

        return $gate;
    }

    /**
     * {@inheritdoc}
     */
    public function findByName(string $name, int $serviceId, int $userId) : Gate {
        $gate = $this->findOneBy(
            [
                'user_id' => $userId,
                'creator' => $serviceId,
                'name'    => $name
            ]
        );

        $category = $this->findCategory($gate);
        $gate->category = $category ? $category->toArray() : null;

        return $gate;
    }

    /**
     * {@inheritdoc}
     */
    public function getByServiceIdAndUserId(int $serviceId, int $userId, array $queryParams = []) : Collection {
        $gates = $this->findBy(
            [
                'creator' => $serviceId,
                'user_id' => $userId
            ], $queryParams
        );

        $gates = $this->associateCategories($gates);

        return $gates;
    }

    /**
     * {@inheritdoc}
     */
    public function getByUserId(int $userId, array $queryParams = []) : Collection {
        $gates = $this->findBy(
            [
                'user_id' => $userId
            ], $queryParams
        );

        $gates = $this->associateCategories($gates);

        return $gates;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByUserId(int $userId) : int {
        return $this->deleteByKey('user_id', $userId);
    }
}
