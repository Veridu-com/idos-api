<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Repository;

use App\Exception\NotFound;
use App\Entity\EntityInterface;
use Illuminate\Support\Collection;

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
    public function save(EntityInterface $entity) {
        $entity->id         = count($this->storage) + 1;
        $entity->created_at = time();
        $this->storage[]    = $entity;
    }

    /**
     * {@inheritDoc}
     */
    public function find($id) {
        if (isset($this->storage[$id]))
            return $this->storage[$id];
        throw new NotFound();
    }

    /**
     * {@inheritDoc}
     */
    public function findByKey($key, $value) {
        foreach ($this->storage as $item)
            if ($item->{$key} === $value)
                return $item;
        throw new NotFound();
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

        return new Collection($return);
    }

    /**
     * {@inheritDoc}
     */
    public function getAll() {
        return new Collection(array_values($this->storage));
    }
}
