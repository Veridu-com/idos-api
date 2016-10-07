<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository\Profile;

use App\Entity\Profile\Gate;
use App\Repository\RepositoryInterface;
use Illuminate\Support\Collection;

/**
 * Gate Repository Interface.
 */
interface GateInterface extends RepositoryInterface {
    /**
     * Returns a gate based on its user id, source id, service id (creator) and slug.
     *
     * @param string      $slug       The gate slug
     * @param int         $serviceId  The service id
     * @param int         $userId     The user id
     *
     * @return Gate
     */
    public function findOne(string $slug, int $serviceId, int $userId) : Gate;

    /**
     * Returns a gate based on its user id, source id, service id (creator) and name.
     *
     * @param string      $name       The gate name
     * @param int         $serviceId  The service id
     * @param int         $userId     The user id
     *
     * @return Gate
     */
    public function findOneByName(string $name, int $serviceId, int $userId) : Gate;

    /**
     * Return gates based on their user id and service id (creator).
     *
     * @param int   $serviceId
     * @param int   $userId
     * @param array $queryParams
     *
     * @return Collection
     */
    public function findByServiceIdAndUserId(int $serviceId, int $userId, array $queryParams = []) : Collection;

    /**
     * Return gates based on their user id.
     *
     * @param int   $userId
     * @param array $queryParams
     *
     * @return Collection
     */
    public function findByUserId(int $userId, array $queryParams = []) : Collection;

    /**
     * Delete gates based on their user id.
     *
     * @param int $userId
     *
     * @return int
     */
    public function deleteByUserId(int $userId) : int;
}
