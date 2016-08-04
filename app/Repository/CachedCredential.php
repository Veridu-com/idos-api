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
<<<<<<< Updated upstream
    protected $entityName = 'Credential';
=======
    public function find($id) {
        return $this->findOneBy(['id' => $id]);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($id) {
        return $this->deleteBy(['id' => $id]);
    }
>>>>>>> Stashed changes

    /**
     * {@inheritdoc}
     */
    public function findByPubKey(string $pubKey) : CredentialEntity {
<<<<<<< Updated upstream
        return $this->repository->findOneBy(['public' => $pubKey]);
=======
        return $this->findOneBy(['public' => $pubKey]);
>>>>>>> Stashed changes
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByCompanyId(string $companyId) : int {
<<<<<<< Updated upstream
        return $this->repository->deleteByKey('company_id', $companyId);
=======
        return $this->deleteBy('company_id', $companyId);
>>>>>>> Stashed changes
    }

    /**
     * {@inheritdoc}
     */
    public function getAllByCompanyId(string $companyId) : Collection {
        return $this->findBy(['company_id' => $companyId]);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByCompanyId($companyId) {
        return $this->deleteBy('company_id', $companyId);
    }
}
