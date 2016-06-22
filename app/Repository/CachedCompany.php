<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Repository;

use Stash\Invalidation;

/**
 * Cache-based Company Repository Implementation.
 */
class CachedCompany extends AbstractCachedRepository implements CompanyInterface {
    /**
     * {@inheritDoc}
     */
    public function find($id) {
        return $this->respository->find($id);
    }

    /**
     * {@inheritDoc}
     */
    public function delete($id) {
        return $this->repository->delete($id);
    }

    /**
     * {@inheritDoc}
     */
    public function getAll() {
        return $this->repository->getAll();
    }

    /**
     * {@inheritDoc}
     */
    public function findByPubKey($pubKey) {
        return $this->repository->findByPubKey($pubKey);
    }

    /**
     * {@inheritDoc}
     */
    public function findByPrivKey($privKey) {
        return $this->repository->findByPrivKey($privKey);
    }

    /**
     * {@inheritDoc}
     */
    public function findBySlug($slug) {
        return $this->repository->findBySlug($slug);
    }

    /**
     * {@inheritDoc}
     */
    public function getAllByParentId($parentId) {
        return $this->repository->getAllByParentId($parentId);
    }

    /**
     * {@inheritDoc}
     */
    public function deleteByParentId($parentId) {
        return $this->repository->deleteByParentId($parentId);
    }
}
