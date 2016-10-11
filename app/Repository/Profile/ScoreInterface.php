<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository\Profile;

use App\Entity\Profile\Score;
use App\Repository\RepositoryInterface;
use Illuminate\Support\Collection;

/**
 * Score Repository Interface.
 */
interface ScoreInterface extends RepositoryInterface {
    /**
     * Returns a score based on its name, service id and user id.
     *
     * @param string $name
     * @param int    $serviceId
     * @param int    $userId
     *
     * @return Score
     */
    public function findOne(string $name, int $serviceId, int $userId) : Score;

    /**
     * Return scores based on their service id and user id.
     *
     * @param int   $serviceId
     * @param int   $userId
     * @param array $queryParams
     *
     * @return Collection
     */
    public function getByUserIdAndServiceId(int $serviceId, int $userId, array $queryParams = []) : Collection;

    /**
     * Return scores based on their user id.
     *
     * @param int   $userId
     * @param array $queryParams
     *
     * @return Collection
     */
    public function getByUserId(int $userId, array $queryParams = []) : Collection;
}
