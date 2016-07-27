<?php

declare(strict_types=1);
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Repository;

use App\Entity\User;
use App\Exception\NotFound;

/**
 * Database-based User Repository Implementation.
 */
class DBUser extends AbstractDBRepository implements UserInterface {
    /**
     * The table associated with the repository.
     *
     * @var string
     */
    protected $tableName = 'users';
    /**
     * The entity associated with the repository.
     *
     * @var string
     */
    protected $entityName = User::class;

    /**
     * {@inheritdoc}
     */
    public function findByUserName($userName, $credentialId) {
        $result = $this->query()
            ->where('username', $userName)
            ->where('credential_id', $credentialId)
            ->first();
        if (empty($result))
            throw new NotFound();

        return $result;
    }
}
