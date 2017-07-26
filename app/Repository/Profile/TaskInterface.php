<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository\Profile;

use App\Repository\RepositoryInterface;

/**
 * Task Repository Interface.
 */
interface TaskInterface extends RepositoryInterface {
    /**
     * Gets all Tasks based on their process id.
     *
     * @param int $processId
     *
     * @return array
     */
    public function getAllByProcessId(int $processId, array $queryParams = []) : array;
}
