<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository\Profile;

use App\Entity\Profile\Reference;
use App\Repository\RepositoryInterface;
use Illuminate\Support\Collection;

/**
 * Reference Repository Interface.
 */
interface ReferenceInterface extends RepositoryInterface {
    /**
     * Returns a reference based on its user id and name.
     *
     * @param string $name
     * @param int    $userId
     * @param int    $userId
     *
     * @return \App\Entity\Profile\Reference
     */
    public function findOne(string $name, int $userId) : Reference;

    /**
     * Return references based on their user id.
     *
     * @param int   $userId
     * @param array $queryParams
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAllByUserId(int $userId, array $queryParams = []) : Collection;

    /**
     * Deletes a reference based on its user id and name.
     *
     * @param string $name
     * @param int    $userId
     * @param int    $userId
     *
     * @return int
     */
    public function deleteOne(string $name, int $userId) : int;

    /**
     * Deletes references based on their user id.
     *
     * @param int $userId
     *
     * @return int
     */
    public function deleteByUserId(int $userId) : int;
}
