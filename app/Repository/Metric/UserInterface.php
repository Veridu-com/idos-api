<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository\Metric;

use App\Entity\Metric\User;
use App\Entity\Identity;
use App\Repository\RepositoryInterface;
use Illuminate\Support\Collection;

/**
 * User Metrics Repository Interface.
 */
interface UserInterface extends RepositoryInterface {
    /**
     * Return user metrics.
     *
     * @param \App\Entity\Identity $identity
     * @param int $from
     * @param int $to
     * @param array $queryParams
     *
     * @return \Illuminate\Support\Collection
     */
    public function getByIdentityAndDateInterval(Identity $identity, int $from, int $to, array $queryParams = []) : Collection;
}
