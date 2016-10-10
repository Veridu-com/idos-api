<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository\Profile;

use App\Entity\Profile\Process;
use App\Repository\RepositoryInterface;

/**
 * Process Repository Interface.
 */
interface ProcessInterface extends RepositoryInterface {
    /**
     * Returns a process based on its user id and id.
     *
     * @param int $id     The process id
     * @param int $userId The user id
     *
     * @return \App\Entity\Profile\Process
     */
    public function findOne(int $id, int $userId) : Process;

    /**
     * Returns a process based on its source id.
     *
     * @param int $id        The feature id
     * @param int $serviceId The service id
     * @param int $userId    The user id
     *
     * @return \App\Entity\Profile\Process
     */
    public function findOneBySourceId(int $sourceId) : Process;

    /**
     * Returns the last process of the user given its source id and event.
     *
     * @param int|null    $source The source
     * @param string|null $event  The event
     * @param int         $userId The user identifier
     *
     * @throws \App\Exception\NotFound
     *
     * @return \App\Entity\Profile\Process
     */
    public function findLastByUserIdSourceIdAndEvent($sourceId, $event, int $userId) : Process;

    /**
     * Return processes based on their user id.
     *
     * @param int   $userId
     * @param array $queryParams
     *
     * @return \Illuminate\Support\Collection
     */
    public function getByUserId(int $userId, array $queryParams = []) : array;
}
