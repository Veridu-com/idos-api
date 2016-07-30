<?php

/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\EntityInterface;
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
    protected $entityName = 'User';

    /**
     * {@inheritdoc}
     */
    public function findByUserNameAndCompany(string $username, int $companyId) : EntityInterface {
        $result = $this->query()
            ->selectRaw('users.*, credentials.company_id')
            ->join('credentials', 'users.credential_id', '=', 'credentials.id')
            ->where('credentials.company_id', '=', $companyId)
            ->first();

        if (empty($result))
            throw new NotFound();

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function findByUserName($username, $credentialId) {
        return $this->findOneBy([
            'username'      => $username,
            'credential_id' => $credentialId
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function findByPrivKey(string $privateKey) {
        $result = $this->query()
            ->selectRaw('users.*')
            ->join('credentials', 'users.credential_id', '=', 'credentials.id')
            ->where('credentials.private', '=', $privateKey)
            ->first();

        if (empty($result))
            throw new NotFound();

        return $result;
    }
}
