<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository\Profile;

use App\Entity\Profile\Flag;
use App\Repository\RepositoryInterface;
use Illuminate\Support\Collection;

/**
 * Flag Repository Interface.
 */
interface FlagInterface extends RepositoryInterface {
    /**
     * Return a flag based on its slug, service id and user id.
     *
     * @param string $slug
     * @param int    $serviceId
     * @param int    $userId
     *
     * @return \App\Entity\Profile\Flag
     */
    public function findOne(string $slug, int $serviceId, int $userId) : Flag;

    /**
     * Returns flags based on their service id and user id.
     *
     * @param int $serviceId
     * @param int $userId
     *
     * @return \Illuminate\Support\Collection
     */
    public function getByUserIdAndServiceId(int $serviceId, int $userId, array $queryParams = []) : Collection;

    /**
     * Returns flags based on their user id.
     *
     * @param int $userId
     *
     * @return \Illuminate\Support\Collection
     */
    public function getByUserId(int $userId, array $queryParams = []) : Collection;
}
