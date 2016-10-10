<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository\Profile;

use App\Entity\Profile\Review;
use App\Repository\RepositoryInterface;
use Illuminate\Support\Collection;

/**
 * Review Repository Interface.
 */
interface ReviewInterface extends RepositoryInterface {
    /**
     * Returns a review based on its id, identity id and user id.
     *
     * @param int $id
     * @param int $identityId
     * @param int $userId
     *
     * @return Review
     */
    public function findOne(int $id, int $identityId, int $userId) : Review;

    /**
     * Gets all Review entities based on their user_id, filtering them by warning_id.
     *
     * @param int $identityId
     * @param int $userId
     * @param array $queryParams
     *
     * @return \Illuminate\Database\Eloquent\Collection|Collection
     */
    public function getByUserIdAndIdentityId(int $identityId, int $userId, array $queryParams = []) : Collection;

    /**
     * Gets all Review entities based on their user_id.
     *
     * @param int $userId
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByUserId(int $userId) : Collection;
}
