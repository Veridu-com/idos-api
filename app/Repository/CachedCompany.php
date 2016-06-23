<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Repository;

/**
 * Cache-based Company Repository Implementation.
 */
class CachedCompany extends AbstractCachedRepository implements CompanyInterface {
    /**
     * {@inheritdoc}
     */
    public function find($id) {
        return $this->respository->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($id) {
        return $this->repository->delete($id);
    }

    /**
     * {@inheritdoc}
     */
    public function getAll() {
        return $this->repository->getAll();
    }

    /**
     * {@inheritdoc}
     */
    public function findByPubKey($pubKey) {
        return $this->repository->findByPubKey($pubKey);
    }

    /**
     * {@inheritdoc}
     */
    public function findByPrivKey($privKey) {
        return $this->repository->findByPrivKey($privKey);
    }

    /**
     * {@inheritdoc}
     */
    public function findBySlug($slug) {
        return $this->repository->findBySlug($slug);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllByParentId($parentId) {
        return $this->repository->getAllByParentId($parentId);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByParentId($parentId) {
        return $this->repository->deleteByParentId($parentId);
    }
}
