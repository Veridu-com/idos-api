<?php

/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Credential as CredentialEntity;
use Illuminate\Support\Collection;

/**
 * Cache-based Credential Repository Implementation.
 */
class CachedCredential extends AbstractCachedRepository implements CredentialInterface {
    /**
     * The entity associated with the repository.
     *
     * @var string
     */
    protected $entityName = 'Credential';

    /**
     * {@inheritdoc}
     */
    public function findByPrivKey($pubKey) : CredentialEntity {
        return $this->findOneBy(['private' => $pubKey]);
    }

    /**
     * {@inheritdoc}
     */
    public function findByPubKey($pubKey) : CredentialEntity {
        return $this->repository->findOneBy(['public' => $pubKey]);
    }
    
    /**
     * {@inheritdoc}
     */
    public function deleteByCompanyId($companyId) : int {
        return $this->repository->deleteByKey('company_id', $companyId);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllByCompanyId($companyId) : Collection {
        return $this->findBy(['company_id' => $companyId]);
    }
}
