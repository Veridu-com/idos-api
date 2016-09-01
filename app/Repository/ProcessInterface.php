<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

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
}
