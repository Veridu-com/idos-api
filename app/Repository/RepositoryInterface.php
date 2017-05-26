<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\EntityInterface;
use Illuminate\Support\Collection;

/**
 * Repository Interface.
 */
interface RepositoryInterface {
    /**
     * Creates a new entity.
     *
     * @param array $attributes
     *
     * @return \App\Entity\EntityInterface
     */
    public function create(array $attributes) : EntityInterface;

    /**
     * Loads an entity.
     *
     * @param array $attributes
     *
     * @return \App\Entity\EntityInterface
     */
    public function load(array $attributes) : EntityInterface;

    /**
     * Saves a new entity.
     *
     * @param \App\Entity\EntityInterface $entity
     *
     * @return \App\Entity\EntityInterface
     */
    public function save(EntityInterface $entity) : EntityInterface;

    /**
     * Find an entity by id.
     *
     * @param int $id
     *
     * @throws \App\Exception\NotFound
     *
     * @return \App\Entity\EntityInterface
     */
    public function find(int $id) : EntityInterface;

    /**
     * Find an entity by a key.
     *
     * @param array $constraints ['key' => 'value']
     * @param array $queryParams
     * @param array $columns
     *
     * @throws \App\Exception\NotFound
     *
     * @return \App\Entity\EntityInterface
     */
    public function findOneBy(array $constraints, array $queryParams, array $columns) : EntityInterface;

    /**
     * Find entities by keys.
     *
     * @param array $constraints ['key' => 'value']
     * @param array $queryParams
     * @param array $columns
     *
     * @throws \App\Exception\NotFound
     *
     * @return \Illuminate\Support\Collection
     */
    public function findBy(array $constraints, array $queryParams, array $columns) : Collection;

    /**
     * Delete an entitiy by id.
     *
     * @param int    $id
     * @param string $key
     *
     * @return int number of affected rows
     */
    public function delete(int $id, string $key = 'id') : int;

    /**
     * Return an entity collection.
     *
     * @param array $queryParams
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAll(array $queryParams = []) : Collection;
}
