<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\Warning;
use Illuminate\Support\Collection;

/**
 * Warning Repository Interface.
 */
interface WarningInterface extends RepositoryInterface {
    /**
     * Gets all Warnings based on their user id.
     *
     * @param int $userId
     *
     * @return Illuminate\Database\Collection
     */
    public function findByUserId(int $userId, array $queryParams = []) : Collection;
    public function findOneBySlug(int $userId, int $serviceId, string $slug) : Warning;
    public function findOneByName(int $userId, int $serviceId, string $name) : Warning;
}
