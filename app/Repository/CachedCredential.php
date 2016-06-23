<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Repository;

/**
 * Cache-based Credential Repository Implementation.
 */
class CachedCredential extends AbstractCachedRepository implements CredentialInterface {
    /**
     * {@inheritdoc}
     */
    public function find($id) {
        return $this->repository->find($id);
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
    public function getAllByCompanyId($companyId) {
        return $this->getAllByCompanyId($companyId);
    }
}
