<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository\Company;

use App\Entity\Company\Subscription;
use App\Repository\RepositoryInterface;
use Illuminate\Support\Collection;

/**
 * Subscription Repository Interface.
 */
interface SubscriptionInterface extends RepositoryInterface {
    /**
     * Gets all subscriptions the by credential identifier.
     *
     * @param int $credentialId The credential identifier
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAllByCredentialId(int $credentialId) : Collection;
}
