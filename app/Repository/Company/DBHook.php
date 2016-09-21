<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository\Company;

use App\Entity\Company\Hook;
use App\Repository\AbstractSQLDBRepository;
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
    protected $entityName = 'Company\Hook';

    /**
     * {@inheritdoc}
     */
    public function getAllByCredentialId(int $credentialId) : Collection {
        return $this->findBy(['credential_id' => $credentialId]);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllByCredentialPubKey(string $credentialPubKey) : Collection {
        return $this->query()
            ->join('credentials', 'credentials.id', 'hooks.credential_id')
            ->where('credentials.public', $credentialPubKey)
            ->get(['hooks.*']);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllByCredentialPubKeyAndCompanyId(string $credentialPubKey, int $companyId) : Collection {
        return $this->query()
            ->join('credentials', 'credentials.id', 'hooks.credential_id')
            ->where('credentials.public', $credentialPubKey)
            ->where('credentials.company_id', $companyId)
            ->get(['hooks.*']);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByCredentialId(int $credentialId) : int {
        return $this->deleteByKey('credential_id', $credentialId);
    }
}
