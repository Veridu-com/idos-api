<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Repository;

use Stash\Invalidation;

/**
 * Cache-based User Repository Implementation.
 */
class CachedUser extends AbstractCachedRepository implements UserInterface {
    /**
     * {@inheritDoc}
     */
    public function find($id) {
        return $this->repository->find($id);
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
    public function findByUserName($userName, $credentialId) {
        return $this->findByUserName($userName, $credentialId);
    }
}
