<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Repository;

use App\Exception\NotFound;
use App\Model\Company;

/**
 * Array-based Company Repository Implementation.
 */
class ArrayCompany extends AbstractArrayRepository implements CompanyInterface {
    /**
     * Class constructor.
     *
     * @param App\Model\Company $model
     *
     * @return void
     */
    public function __construct(Company $model) {
        $this->model = $model;
    }

    /**
     * {@inheritDoc}
     */
    public function findBySlug($slug) {
        foreach ($this->storage as $item)
            if ($item->slug === $slug)
                return $item;
        throw new NotFound(get_class($this->model));
    }

    /**
     * {@inheritDoc}
     */
    public function findByPubKey($pubKey) {
        foreach ($this->storage as $item)
            if ($item->public_key === $pubKey)
                return $item;
        throw new NotFound(get_class($this->model));
    }

    /**
     * {@inheritDoc}
     */
    public function findByPrivKey($privKey) {
        foreach ($this->storage as $item)
            if ($item->private_key === $privKey)
                return $item;
        throw new NotFound(get_class($this->model));
    }

    /**
     * {@inheritDoc}
     */
    public function getAllByParentId($parentId) {
        $return = [];
        foreach ($this->storage as $item)
            if ($item->parent_id === $parentId)
                $return[] = $item;

        return $this->model->newCollection($return);
    }

    /**
     * {@inheritDoc}
     */
    public function deleteByParentId($parentId) {
        foreach ($this->storage as $index => $item)
            if ($item->parent_id === $parentId)
                unset($this->storage[$index]);
    }
}
