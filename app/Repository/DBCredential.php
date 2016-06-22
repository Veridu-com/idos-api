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
        $result = $this->query()
            ->where('public', $pubKey)
            ->first();
        if (empty($result))
            throw new NotFound();

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function getAllByCompanyId($companyId) {
        return new Collection(
            $this->query()
                ->where('company_id', $companyId)
                ->get()
        );
    }
}
