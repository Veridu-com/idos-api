<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\Review;
use Illuminate\Support\Collection;

/**
 * Review Repository Interface.
 */
interface ReviewInterface extends RepositoryInterface {
    /**
     * Gets all Review entities based on their user_id.
     *
     * @param int $userId
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllByUserId(int $userId) : Collection;
    /**
     * Gets all Review entities based on their user_id, filtering them by warning_id.
     *
     * @param int   $userId
     * @param array $warningIds
     * @param int   $identityId
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllByUserIdAndWarningIdsAndIdentity(int $userId, array $warningIds, int $identityId) : Collection;
    /**
     * Find a Review entity based on its user_id and id.
     *
     * @param int $userId
     * @param int $id
     * @param int $identityId
     *
     * @throws App\Exception\NotFound
     *
     * @return App\Entity\Review
     */
    public function findOneByUserIdAndIdAndIdentityId(int $userId, int $id, int $identityId) : Review;
}
