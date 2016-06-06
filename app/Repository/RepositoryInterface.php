<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Repository;

use Illuminate\Database\Eloquent\Model;

/**
 * Repository Interface.
 */
interface RepositoryInterface {
    /**
     * Creates a new entity.
     *
     * @param array $attributes
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(array $attributes);

    /**
     * Saves a new entity.
     *
     * @param \Illuminate\Database\Eloquent\Model
     *
     * @return void
     */
    public function save(Model $model);

    /**
     * Find an entity by id.
     *
     * @param int $id
     *
     * @throws App\Exception\NotFound
     *
     * @return \Illuminate\Database\Eloquent\Model
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
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function findByKey($key, $value);

    /**
     * Delete an entitiy by id.
     *
     * @param int $id
     *
     * @return void
     */
    public function delete($id);

    /**
     * Delete all entities that a key matches a value.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return void
     */
    public function deleteByKey($key, $value);

    /**
     * Return all entities that a key matches a value.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllByKey($key, $value);

    /**
     * Return all entities.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll();
}
