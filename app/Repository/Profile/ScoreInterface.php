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
     * @return \App\Entity\Profile\Score
     */
    public function findOne(string $name, int $serviceId, int $userId) : Score;

    /**
     * Return scores based on their service id and user id.
     *
     * @param int   $serviceId
     * @param int   $userId
     * @param array $queryParams
     *
     * @return \Illuminate\Support\Collection
     */
    public function getByUserIdAndServiceId(int $serviceId, int $userId, array $queryParams = []) : Collection;

    /**
     * Return scores based on their user id.
     *
     * @param int   $userId
     * @param array $queryParams
     *
     * @return \Illuminate\Support\Collection
     */
    public function getByUserId(int $userId, array $queryParams = []) : Collection;

    /**
     * Creates or updates a score.
     *
     * @param int    $serviceId
     * @param int    $userId
     * @param string $name
     * @param string $attribute
     * @param float  $value
     *
     * @return \App\Entity\Profile\Score
     */
    public function upsertOne(int $serviceId, int $userId, string $name, string $attribute, float $value) : Score;
}
