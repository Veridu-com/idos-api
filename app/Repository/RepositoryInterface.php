<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Repository;

use App\Entity\EntityInterface;

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
    public function create(array $attributes);

    /**
     * Saves a new entity.
     *
     * @param App\Entity\EntityInterface $entity
     *
     * @return void
     */
    public function save(EntityInterface &$entity);

    /**
     * Find an entity by id.
     *
     * @param int $id
     *
     * @throws App\Exception\NotFound
     *
     * @return App\Entity\EntityInterface
     */
    public function find($id);

    /**
     * Find the first entity that a key matches value.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @throws App\Exception\NotFound
     *
     * @return App\Entity\EntityInterface
     */
    public function findByKey($key, $value);

    /**
     * Delete an entitiy by id.
     *
     * @param int $id
     *
     * @return int
     */
    public function delete($id);

    /**
     * Delete all entities that a key matches a value.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return int
     */
    public function deleteByKey($key, $value);

    /**
     * Return an entity collection with all entities that a key matches a value.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAllByKey($key, $value);

    /**
     * Return an entity collection.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAll();
}
