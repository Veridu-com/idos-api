<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository\Profile;

use App\Entity\Profile\Candidate;
use App\Repository\RepositoryInterface;
use Illuminate\Support\Collection;

/**
 * Candidate Repository Interface.
 */
interface CandidateInterface extends RepositoryInterface {
    /**
     * Returns all features based on their user id.
     *
     * @param int   $userId
     * @param array $filters
     *
     * @return \Illuminate\Support\Collection
     */
    public function findByUserId(int $userId, array $filters = []) : Collection;

    /**
     * Gets all Candidate entities based on their user_id, filtering them by name.
     *
     * @param int   $userId
     * @param array $filters
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllByUserIdAndNames(int $userId, array $filters = []) : Collection;

    /**
     * Deletes all Candidate entities based on user_id.
     *
     * @param int $userId
     *
     * @return int
     */
    public function deleteByUserId(int $userId, array $filters = []) : int;

    /**
     * Find a Candidate entity based on its user_id and attribute name.
     *
     * @param int    $userId
     * @param string $attributeName
     *
     * @throws \App\Exception\NotFound
     *
     * @return \App\Entity\Profile\Candidate
     */
    public function findOneByUserIdAndAttributeName(int $userId, string $attributeName) : Candidate;

    /**
     * Deletes a Candidate entity based on their user_id and name.
     *
     * @param int    $userId
     * @param string $name
     *
     * @return int
     */
    public function deleteOneByUserIdAndName(int $userId, string $name) : int;
}
