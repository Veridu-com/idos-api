<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\Warning;
use Illuminate\Support\Collection;

/**
 * Warning Repository Interface.
 */
interface WarningInterface extends RepositoryInterface {
    /**
     * Gets all Warnings based on their user id.
     *
     * @param int $userId
     *
     * @return array
     */
    public function getAllByUserId(int $userId, array $queryParams = []) : array;

    /**
     * Deletes all warnings based on their user id.
     *
     * @param int $userId
     *
     * @return int
     */
    public function deleteByUserId(int $userId) : int;

    /**
     * Returns a collection of warnings based on their user id.
     *
     * @param int $userId
     *
     * @return Illuminate\Support\Collection
     */
    public function findByUserId(int $userId) : Collection;

    /**
     * Returns a Warning based on the user id and the slug.
     *
     * @param int    $userId      The user identifier
     * @param string $warningSlug The warning slug
     */
    public function findByUserIdAndSlug(int $userId, string $warningSlug) : Warning;
}
