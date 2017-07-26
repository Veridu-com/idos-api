<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository\Profile;

use App\Entity\Profile\Feature;
use App\Repository\RepositoryInterface;
use Illuminate\Support\Collection;

/**
 * Feature Repository Interface.
 */
interface FeatureInterface extends RepositoryInterface {
    /**
     * Returns a feature based on its user id, service id (creator) and id.
     *
     * @param int $id        The feature id
     * @param int $handlerId The service id
     * @param int $userId    The user id
     *
     * @return \App\Entity\Profile\Feature
     */
    public function findOne(int $id, int $handlerId, int $userId) : Feature;

    /**
     * Returns a feature based on its user id, source id, service id (creator) and name.
     *
     * @param string      $name       The feature name
     * @param int         $handlerId  The service id
     * @param string|null $sourceName The source name
     * @param int         $userId     The user id
     *
     * @return \App\Entity\Profile\Feature
     */
    public function findOneByName(string $name, int $handlerId, $sourceName, int $userId) : Feature;

    /**
     * Returns a feature based on its user id and id.
     *
     * @param int $id     The feature id
     * @param int $userId The user id
     *
     * @return \App\Entity\Profile\Feature
     */
    public function findOneByIdAndUserId(int $id, int $userId) : Feature;

    /**
     * Return features based on their user id and service id (creator).
     *
     * @param int   $handlerId
     * @param int   $userId
     * @param array $queryParams
     *
     * @return \Illuminate\Support\Collection
     */
    public function getByHandlerIdAndUserId(int $handlerId, int $userId, array $queryParams = []) : Collection;

    /**
     * Return features based on their user id.
     *
     * @param int   $userId
     * @param array $queryParams
     *
     * @return \Illuminate\Support\Collection
     */
    public function getByUserId(int $userId, array $queryParams = []) : Collection;

    /**
     * Return features based on their user id and names.
     *
     * @param int   $userId
     * @param array $featureNames
     *
     * @return \Illuminate\Support\Collection
     */
    public function getByUserIdAndNames(int $userId, array $featureNames = []) : Collection;

    /**
     * Delete features based on their user id.
     *
     * @param int   $userId
     * @param array $queryParams
     *
     * @return int
     */
    public function deleteByUserId(int $userId, array $queryParams = []) : int;
}
