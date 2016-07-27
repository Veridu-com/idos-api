<?php

/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Repository;

/**
 * Cache-based User Repository Implementation.
 */
class CachedUser extends AbstractCachedRepository implements UserInterface {
    /**
     * The entity associated with the repository.
     *
     * @var string
     */
    protected $entityName = 'User';

    /**
     * {@inheritdoc}
     */
    public function findByUserName($userName, $credentialId) {
        return $this->findByUserName($userName, $credentialId);
    }
}
