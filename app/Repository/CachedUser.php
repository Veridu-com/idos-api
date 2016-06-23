<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Repository;

/**
 * Cache-based User Repository Implementation.
 */
class CachedUser extends AbstractCachedRepository implements UserInterface {
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
    public function findByUserName($userName, $credentialId) {
        return $this->findByUserName($userName, $credentialId);
    }
}
