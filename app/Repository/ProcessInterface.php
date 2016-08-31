<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\Process;
use Illuminate\Support\Collection;

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
     * Returns a single process by id with all associated tasks
     * filtered by query parameters
     *
     * @param      integer  $userId       The user identifier
     * @param      array    $queryParams  The query parameters
     *
     * @return     array   paginated array
     */
    public function findWithTasks(int $id, array $queryParams = []) : Process;
}
