<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\Identity;
use Illuminate\Support\Collection;

/**
 * Identity Repository Interface.
 */
interface IdentityInterface extends RepositoryInterface {
    /**
     * Finds an Identity based on its Public Key.
     *
     * @param string $pubKey
     *
     * @throws App\Exception\NotFound
     *
     * @return App\Entity\Identity
     */
    public function findByPubKey(string $pubKey) : Identity;

    /**
     * Finds an Identity based on its $sourceName and $profileId (aka. reference).
     *
     * @param string $sourceName
     * @param string $profileId
     *
     * @return \Illuminate\Support\Collection
     */
    public function findBySourceNameAndProfileId(string $sourceName, string $profileId, string $applicationId) : Collection;
}
