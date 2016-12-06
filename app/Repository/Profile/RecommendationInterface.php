<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository\Profile;

use App\Entity\Profile\Recommendation;
use App\Repository\RepositoryInterface;

/**
 * Recommendation Repository Interface.
 */
interface RecommendationInterface extends RepositoryInterface {
    /**
     * Returns a recommendation based on its user id.
     *
     * @param int $userId The user id
     *
     * @return \App\Entity\Profile\Recommendation
     */
    public function findOne(int $userId) : Recommendation;

    /**
     * Creates or updates a recommendation.
     *
     * @param int   $userId
     * @param int   $serviceId
     * @param bool  $result
     * @param array $reasons
     *
     * @return \App\Entity\Profile\Recommendation
     */
    public function upsertOne(int $userId, int $serviceId, bool $result, array $reasons) : Recommendation;
}
