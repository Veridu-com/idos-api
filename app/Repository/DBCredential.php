<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Repository;

use App\Entity\Credential;

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
    protected $entityName = 'Credential';

    /**
     * {@inheritdoc}
     */
    public function findByPubKey($pubKey) {
        return $this->findByKey('public', $pubKey);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllByCompanyId($companyId) {
        return $this->getAllByKey('company_id', $companyId);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByCompanyId($companyId) {
        return $this->deleteByKey('company_id', $companyId);
    }
    /**
     * {@inheritdoc}
     */
    public function deleteByPubKey($pubKey) {
        return $this->deleteByKey('public', $pubKey);
    }
}
