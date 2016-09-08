<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\User;
use Illuminate\Support\Collection;

/**
 * User Repository Interface.
 */
interface UserInterface extends RepositoryInterface {
    /**
     * Find all users that belongs to specified company.
     *
     * @param int    $companyId
     *
     * @throws App\Exception\NotFound
     *
     * @return Illuminate\Support\Collection
     */
    public function findByCompanyId(int $companyId) : Collection;

    /**
     * Find a user by username.
     *
     * @param string $userName
     * @param int    $credentialId
     *
     * @throws App\Exception\NotFound
     *
     * @return App\Entity\User
     */
    public function findByUserName(string $userName, int $credentialId) : User;

    /**
     * Gets a username by profile id, provider name and credential id.
     *
     * @param string $profileId    The profile id
     * @param string $providerName The provider name
     * @param int    $credentialId The credential id
     *
     * @return string A username by profile identifier, provider name and credential id.
     */
    public function getUserNameByProfileIdAndProviderNameAndCredentialId(string $profileId, string $providerName, int $credentialId) : string;
}
