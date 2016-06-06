<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Repository;

use App\Exception\NotFound;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Abstract Database-based Repository.
 */
abstract class AbstractDBRepository extends AbstractRepository {
    /**
     * {@inheritDoc}
     */
    public function save(Model $model) {
        $model->saveOrFail();
    }

    /**
     * {@inheritDoc}
     */
    public function find($id) {
        try {
            return $this->model->findOrFail($id);
        } catch (ModelNotFoundException $exception) {
            throw new NotFound(get_class($this->model));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function findByKey($key, $value) {
        try {
            $this->model->where($key, $value)->firstOrFail();
        } catch (ModelNotFoundException $exception) {
            throw new NotFound(get_class($this->model));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function delete($id) {
        try {
            $this->model->find($id)->delete();
        } catch (ModelNotFoundException $exception) {
        }
    }

    /**
     * {@inheritDoc}
     */
    public function deleteByKey($key, $value) {
        try {
            $this->model->where($key, $value)->delete();
        } catch (ModelNotFoundException $exception) {
            throw new NotFound(get_class($this->model));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getAllByKey($key, $value) {
        try {
            $this->model->where($key, $value)->get();
        } catch (ModelNotFoundException $exception) {
            throw new NotFound(get_class($this->model));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getAll() {
        return $this->model->all();
    }
}
