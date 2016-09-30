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
     * Gets all Processes based on their user id.
     *
     * @param int $userId
     *
     * @return array
     */
    public function getAllByUserId(int $userId, array $queryParams = []) : array;

    /**
     * Finds one by source id.
     *
     * @param int $sourceId The source identifier
     *
     * @return App\Entity\Profile\Process
     */
    public function findOneBySourceId(int $sourceId) : Process;

    /**
     * Finds the last process of the user with nullable source and events.
     *
     * @param int         $userId The user identifier
     * @param int|null    $source The source
     * @param string|null $event  The event
     *
     * @throws App\Exception\NotFound
     *
     * @return App\Entity\Profile\Process
     */
    public function findLastByUserIdSourceIdAndEvent(int $userId, $sourceId, $event) : Process;
}
