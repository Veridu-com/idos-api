<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\Hook;
use Illuminate\Support\Collection;

/**
 * Database-based Hook Repository Implementation.
 */
class DBHook extends AbstractSQLDBRepository implements HookInterface {
    /**
     * The table associated with the repository.
     *
     * @var string
     */
    protected $tableName = 'hooks';
    /**
     * The entity associated with the repository.
     *
     * @var string
     */
    protected $entityName = 'Hook';

    /**
     * {@inheritdoc}
     */
    public function getAllByCredentialId(int $credentialId) : Collection {
        return $this->findBy(['credential_id' => $credentialId]);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByCredentialId(int $credentialId) : int {
        return $this->deleteByKey('credential_id', $credentialId);
    }
}
