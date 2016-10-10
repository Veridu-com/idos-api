<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\Company;
use App\Entity\Company\Credential;
use App\Entity\User;
use Illuminate\Support\Collection;

/**
 * User Repository Interface.
 */
interface UserInterface extends RepositoryInterface {
    /**
     * Find all users that belongs to specified company.
     *
     * @param int $companyId
     *
     * @throws App\Exception\NotFound
     *
     * @return Illuminate\Support\Collection
     */
    public function findByCompanyId(int $companyId) : Collection;

    // FIXME Whoever coded me didn't put love in my documentation </3
    public function findByUserNameAndCompany(string $userName, int $companyId) : User;

/**
 * Finds a user by its $userName and $credentialId.
 *
 * @param string $userName
 * @param int    $credentialId
 *
 * @throws App\Exception\NotFound
 *
 * @return App\Entity\User
 */
    // FIXME Whoever coded me didn't put love in my documentation </3
    public function findByUserName(string $userName, int $credentialId) : User;

    // FIXME Whoever coded me didn't put love in my documentation </3
    public function findOrCreate(string $userName, int $credentialId) : User;

    // FIXME Whoever coded me didn't put love in my documentation </3
    public function findOneByUsernameAndCredentialId(string $userName, int $credentialId) : User;

    // FIXME Whoever coded me didn't put love in my documentation </3
    public function findAllRelatedToCompany(User $user, Company $company) : Collection;

    /**
     * Gets a user by profile id, provider name and credential id.
     *
     * @param string $profileId    The profile id
     * @param string $providerName The provider name
     * @param int    $credentialId The credential id
     * 
     * @throws App\Exception\NotFound\UserException
     *
     * @return App\Entity\User A user entity
     */
    public function findOneByProfileIdAndProviderNameAndCredentialId(
        string $profileId,
        string $providerName,
        int $credentialId
    ) : User;

    /**
     * Finds all users that belongs to an $identityId.
     *
     * @param int $identityId
     *
     * @throws App\Exception\NotFound
     *
     * @return \Illuminate\Support\Collection
     */
    public function findAllByIdentityId(int $identityId) : Collection;

    /**
     * Finds a user by its $identityId and $companyId.
     *
     * @param int $identityId
     * @param int $companyId
     *
     * @throws App\Exception\NotFound
     *
     * @return App\Entity\User
     */
    public function findOneByIdentityIdAndCompanyId(int $identityId, int $companyId) : User;

    /**
     * Assigns an identity to a user.
     *
     * @param int $userId     The user identifier
     * @param int $identityId The identity identifier
     * 
     * @throws \Illuminate\Database\QueryException
     *
     * @return void
     */
    public function assignIdentityToUser(int $userId, int $identityId);

}
