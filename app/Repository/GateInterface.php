<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\Gate;
use Illuminate\Support\Collection;

/**
 * Gate Repository Interface.
 */
interface GateInterface extends RepositoryInterface {
    /**
     * Gets all Gates based on their user id.
     *
     * @param int $userId
     *
     * @return array
     */
    public function getAllByUserId(int $userId, array $queryParams = []) : array;

    /**
     * Deletes all gates based on their user id.
     *
     * @param int $userId
     *
     * @return int
     */
    public function deleteByUserId(int $userId) : int;

    /**
     * Returns a collection of gates based on their user id.
     *
     * @param int $userId
     *
     * @return Illuminate\Support\Collection
     */
    public function findByUserId(int $userId) : Collection;

    /**
     * Returns a Gate based on the user id and the slug.
     *
     * @param int    $userId   The user identifier
     * @param string $gateSlug The gate slug
     */
    public function findByUserIdAndSlug(int $userId, string $gateSlug) : Gate;
}
