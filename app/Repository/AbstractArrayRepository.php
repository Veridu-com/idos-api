<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Repository;

use App\Exception\NotFound;
use Illuminate\Database\Eloquent\Model;

/**
 * Abstract Array-based Repository.
 */
abstract class AbstractArrayRepository extends AbstractRepository {
    /**
     * Data Storage.
     *
     * @var array
     */
    protected $storage = [];

    /**
     * {@inheritDoc}
     */
    public function save(Model $model) {
        $model->created_at = time();
        $this->storage[]   = $model;
    }

    /**
     * {@inheritDoc}
     */
    public function find($id) {
        if (isset($this->storage[$id]))
            return $this->storage[$id];
        throw new NotFound(get_class($this->model));
    }

    /**
     * {@inheritDoc}
     */
    public function findByKey($key, $value) {
        foreach ($this->storage as $item)
            if ($item->{$key} === $value)
                return $item;
        throw new NotFound(get_class($this->model));
    }

    /**
     * {@inheritDoc}
     */
    public function delete($id) {
        if (isset($this->storage[$id]))
            unset($this->storage[$id]);
    }

    /**
     * {@inheritDoc}
     */
    public function deleteByKey($key, $value) {
        foreach ($this->storage as $index => $item)
            if ($item->{$key} === $value)
                unset($this->storage[$index]);
    }

    /**
     * {@inheritDoc}
     */
    public function getAllByKey($key, $value) {
        $return = [];
        foreach ($this->storage as $item)
            if ($item->{$key} === $value)
                $return[] = $item;

        return $this->model->newCollection($return);
    }

    /**
     * {@inheritDoc}
     */
    public function getAll() {
        return $this->model->newCollection(array_values($this->storage));
    }
}
