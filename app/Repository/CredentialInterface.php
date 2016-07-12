<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Repository;

/**
 * Credential Repository Interface.
 */
interface CredentialInterface extends RepositoryInterface {
    /**
     * Finds a Credential based on its Public Key.
     *
     * @param string $pubKey
     *
     * @throws App\Exception\NotFound
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function findByPubKey($pubKey);
    /**
     * Gets all Credentials based on their Company Id.
     *
     * @param int $companyId
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllByCompanyId($companyId);
    /**
     * Deletes all Credentials based on their Company Id.
     *
     * @param int $companyId
     *
     * @return int
     */
    public function deleteByCompanyId($companyId);
}
