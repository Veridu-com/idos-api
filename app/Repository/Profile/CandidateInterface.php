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
     * Returns all Candidate entities based on the user id.
     *
     * @param int   $userId
     * @param array $filters
     *
     * @return \Illuminate\Support\Collection
     */
    public function findByUserId(int $userId, array $filters = []) : Collection;

    /**
     * Gets all Candidate entities based on the user_id, filtering them by attribute name.
     *
     * @param int   $userId
     * @param array $attributeNames
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAllByUserIdAndAttributeNames(int $userId, array $attributeNames = []) : Collection;

    /**
     * Deletes all Candidate entities based on user_id.
     *
     * @param int   $userId
     * @param array $filters
     *
     * @return int
     */
    public function deleteByUserId(int $userId, array $filters = []) : int;

    /**
     * Find a Candidate entity based on the user_id and attribute name.
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
     * Deletes a Candidate entity based on the user_id and name.
     *
     * @param int    $userId
     * @param string $attributeName
     *
     * @return int
     */
    public function deleteOneByUserIdAndAttributeName(int $userId, string $attributeName) : int;
}
