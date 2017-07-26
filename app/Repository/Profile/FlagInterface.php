<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
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
     * @param int    $handlerId
     * @param int    $userId
     *
     * @return \App\Entity\Profile\Flag
     */
    public function findOne(string $slug, int $handlerId, int $userId) : Flag;

    /**
     * Returns flags based on their service id and user id.
     *
     * @param int   $handlerId
     * @param int   $userId
     * @param array $queryParams
     *
     * @return \Illuminate\Support\Collection
     */
    public function getByUserIdAndHandlerId(int $handlerId, int $userId, array $queryParams = []) : Collection;

    /**
     * Returns flags based on their user id.
     *
     * @param int   $userId
     * @param array $queryParams
     *
     * @return \Illuminate\Support\Collection
     */
    public function getByUserId(int $userId, array $queryParams = []) : Collection;
}
