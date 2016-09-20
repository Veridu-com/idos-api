<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
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
     * Gets all Reference entities based on their user_id.
     *
     * @param int $userId
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllByUserId(int $userId) : Collection;
    /**
     * Gets all Reference entities based on their user_id, filtering them by name.
     *
     * @param int   $userId
     * @param array $queryParams
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllByUserIdAndNames(int $userId, array $queryParams = []) : Collection;
    /*
     * Deletes all Reference entities based on user_id.
     *
     * @param int $userId
     *
     * @return int
     */
    public function deleteByUserId(int $userId) : int;
    /**
     * Find a Reference entity based on its user_id and name.
     *
     * @param int    $userId
     * @param string $name
     *
     * @throws App\Exception\NotFound
     *
     * @return App\Entity\Profile\Reference
     */
    public function findOneByUserIdAndName(int $userId, string $name) : Reference;
    /**
     * Deletes a Reference entity based on their user_id and name.
     *
     * @param int    $userId
     * @param string $name
     *
     * @return int
     */
    public function deleteOneByUserIdAndName(int $userId, string $name) : int;
}
