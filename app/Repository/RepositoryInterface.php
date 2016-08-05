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
     * @return App\Entity\EntityInterface
     */
    public function create(array $attributes) : EntityInterface;

    /**
     * Saves a new entity.
     *
     * @param App\Entity\EntityInterface $entity
     *
     * @return void
     */
    public function save(EntityInterface &$entity) : EntityInterface;

    /**
     * Find an entity by id.
     *
     * @param int $id
     *
     * @throws App\Exception\NotFound
     *
     * @return App\Entity\EntityInterface
     */
    public function find(int $id) : EntityInterface;

    /**
     * Find an entity by a key.
     *
     * @param associative array $constraints ['key' => 'value']
     *
     * @throws App\Exception\NotFound
     *
     * @return App\Entity\EntityInterface
     */
    public function findOneBy(array $constraints) : EntityInterface;

    /**
     * Find entities by keys.
     *
     * @param associative array $constraints ['key' => 'value']
     *
     * @throws App\Exception\NotFound
     *
     * @return Illuminate\Support\Collection
     */
    public function findBy(array $constraints) : Collection;

    /**
     * Delete an entitiy by id.
     *
     * @param int $id
     *
     * @return int number of affected rows
     */
    public function delete(int $id, string $key = 'id') : int;

    /**
     * Return an entity collection.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAll() : Collection;
}
