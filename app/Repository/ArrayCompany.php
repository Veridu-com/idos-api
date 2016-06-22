<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Repository;

use App\Exception\NotFound;
use Illuminate\Support\Collection;

/**
 * Array-based Company Repository Implementation.
 */
class ArrayCompany extends AbstractArrayRepository implements CompanyInterface {
    /**
     * {@inheritDoc}
     */
    public function findBySlug($slug) {
        foreach ($this->storage as $item)
            if ($item->slug === $slug)
                return $item;
        throw new NotFound();
    }

    /**
     * {@inheritDoc}
     */
    public function findByPubKey($pubKey) {
        foreach ($this->storage as $item)
            if ($item->public_key === $pubKey)
                return $item;
        throw new NotFound();
    }

    /**
     * {@inheritDoc}
     */
    public function findByPrivKey($privKey) {
        foreach ($this->storage as $item)
            if ($item->private_key === $privKey)
                return $item;
        throw new NotFound();
    }

    /**
     * {@inheritDoc}
     */
    public function getAllByParentId($parentId) {
        $return = [];
        foreach ($this->storage as $item)
            if ($item->parent_id === $parentId)
                $return[] = $item;

        return new Collection($return);
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
