<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Repository;

use App\Exception\NotFound;
use App\Entity\Credential;
use Illuminate\Support\Collection;

/**
 * Database-based Credential Repository Implementation.
 */
class DBCredential extends AbstractDBRepository implements CredentialInterface {
    /**
     * The table associated with the repository.
     *
     * @var string
     */
    protected $tableName = 'credentials';
    /**
     * The entity associated with the repository.
     *
     * @var string
     */
    protected $entityName = Credential::class;

    /**
     * {@inheritDoc}
     */
    public function findByPubKey($pubKey) {
        return $this->findByKey('public', $pubKey);
    }

    /**
     * {@inheritDoc}
     */
    public function getAllByCompanyId($companyId) {
        return $this->getAllByKey('company_id', $companyId);
    }

    /**
     * {@inheritDoc}
     */
    public function deleteByCompanyId($companyId) {
        return $this->deleteByKey('company_id', $companyId);
    }
}
