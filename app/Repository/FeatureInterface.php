<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\Feature;
use Illuminate\Support\Collection;

/**
 * Feature Repository Interface.
 */
interface FeatureInterface extends RepositoryInterface {
    /**
     * Gets all Features based on their user id.
     *
     * @param int $userId
     *
     * @return array
     */
    public function getAllByUserId(int $userId, array $queryParams = []) : array;

    /**
     * Deletes all features based on their user id.
     *
     * @param int $userId
     *
     * @return int
     */
    public function deleteByUserId(int $userId) : int;

    /**
     * Returns a collection of features based on their user id.
     *
     * @param int $userId
     *
     * @return Illuminate\Support\Collection
     */
    public function findByUserId(int $userId) : Collection;

    /**
     * Updates a feature.
     *
     * @param App\Entity\Feature $feature instance
     *
     * @return int
     */
    public function update(Feature &$feature) : int;

    /**
     * Returns a Feature based on the user id and the slug.
     *
     * @param int    $userId      The user identifier
     * @param string $featureSlug The feature slug
     */
    public function findByUserIdAndSlug(int $userId, string $featureSlug) : Feature;
}
