<?php
/*
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

    public function findByPrivKey($pubKey) {
        return $this->findOneBy(['private' => $pubKey]);
    }
    
    /**
     * {@inheritdoc}
     */
    public function deleteByCompanyId($companyId) {
        return $this->repository->deleteByKey('company_id', $companyId);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllByCompanyId($companyId) {
        return $this->getAllByCompanyId($companyId);
    }
}
