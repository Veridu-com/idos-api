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
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllByUserIdAndWarningIds(int $userId, array $warningIds) : Collection;
}
