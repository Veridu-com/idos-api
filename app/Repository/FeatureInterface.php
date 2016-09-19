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
     * Returns all features based on their user id.
     *
     * @param int   $userId
     * @param array $queryParams
     *
     * @return Collection
     */
    public function findByUserId(int $userId, array $queryParams = []) : Collection;

    /**
     * Deletes all features based on their user id.
     *
     * @param int   $userId
     * @param array $queryParams
     *
     * @return int
     */
    public function deleteByUserId(int $userId, array $queryParams = []) : int;

    /**
     * Returns a feature based on its user id, source id, service id (creator) and id.
     *
     * @param int    $userId     The user id
     * @param string $sourceName The source name
     * @param int    $serviceId  The service id
     * @param int    $id         The feature id
     */
    public function findOneById(int $userId, string $sourceName, int $serviceId, int $id) : Feature;

    /**
     * Returns a feature based on its user id, source id, service id (creator) and name.
     *
     * @param int    $userId     The user id
     * @param string $sourceName The source name
     * @param int    $serviceId  The service id
     * @param string $name       The feature name
     */
    public function findOneByName(int $userId, string $sourceName, int $serviceId, string $name) : Feature;
}
