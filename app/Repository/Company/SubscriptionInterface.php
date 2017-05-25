<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository\Company;

use App\Repository\RepositoryInterface;
use Illuminate\Support\Collection;

/**
 * Subscription Repository Interface.
 */
interface SubscriptionInterface extends RepositoryInterface {
    /**
     * Return subscriptions based on their credential id.
     *
     * @param int $credentialId The credential identifier
     *
     * @return \Illuminate\Support\Collection
     */
    public function getByCredentialId(int $credentialId) : Collection;
}
