<?php

/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

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
    protected $entityName = 'Credential';

    /**
     * {@inheritdoc}
     */
    public function findByPubKey($pubKey) : Credential {
        return $this->findOneBy(['public' => $pubKey]);
    }

    public function findByPrivKey($pubKey) : Credential {
        return $this->findOneBy(['private' => $pubKey]);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllByCompanyId($companyId) : Collection {
        return $this->findBy(['company_id' => $companyId]);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByCompanyId($companyId) : int {
        return $this->deleteByKey('company_id', $companyId);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByPubKey($pubKey) : int {
        return $this->deleteByKey('public', $pubKey);
    }
}
