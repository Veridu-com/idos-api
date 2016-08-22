<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Credential;
use Illuminate\Support\Collection;

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
     * @return App\Entity\Credential
     */
    public function findByPubKey(string $pubKey) : Credential;

    /**
     * Gets all Credentials based on their Company Id.
     *
     * @param int $companyId
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllByCompanyId(int $companyId) : Collection;

    /**
     * Deletes all Credentials based on their Company Id.
     *
     * @param int $companyId
     *
     * @return int
     */
    public function deleteByCompanyId(int $companyId) : int;

    /**
     * Finds a credential by its private key.
     *
     * @param string $key The key
     *
     * @return App\Entity\Credential
     */
    public function findByPrivKey(string $key) : Credential;

    /**
     * Deletes a set of credentials with the given public key.
     *
     * @param string $key The key
     *
     * @return int the amount of deleted credentials
     */
    public function deleteByPubKey(string $key) : int;

    /**
     * Finds one credential with the given company id and public key.
     *
     * @param int    $companyId The company identifier
     * @param string $key       The key
     *
     * @return App/Entity/Credential
     */
    public function findOneByCompanyIdAndPubKey(int $companyId, string $key) : Credential;
    
    /**
     * Generates a signed JWT.
     *
     * @param      string  $subjectCredentialPubKey           The public key of subject Credential
     * @param      string  $issuerCredentialPrivKey  The private key of issuer Credential
     * @param      string  $issuerCredentialPubKey   The public key of issuer Credential
     */
    public function generateToken(string $subjectCredentialPubKey, string $issuerCredentialPrivKey, string $issuerCredentialPubKey) : string;
}
