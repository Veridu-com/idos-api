<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository\Profile;

use App\Entity\Profile\Attribute;
use App\Repository\RepositoryInterface;
use Illuminate\Support\Collection;

/**
 * Attribute Repository Interface.
 */
interface AttributeInterface extends RepositoryInterface {
    /**
     * Returns all features based on their user id.
     *
     * @param int   $userId
     * @param array $queryParams
     *
     * @return Collection
     */
    public function findByUserId(int $userId, array $queryParams = []) : Collection;

    /**
     * Gets all Attribute entities based on their user_id, filtering them by name.
     *
     * @param int   $userId
     * @param array $filters
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllByUserIdAndNames(int $userId, array $filters = []) : Collection;
    /*
     * Deletes all Attribute entities based on user_id.
     *
     * @param int $userId
     *
     * @return int
     */
    public function deleteByUserId(int $userId, array $filters = []) : int;
    /**
     * Find a Attribute entity based on its user_id and name.
     *
     * @param int    $userId
     * @param string $name
     *
     * @throws App\Exception\NotFound
     *
     * @return App\Entity\Profile\Attribute
     */
    public function findOneByUserIdAndName(int $userId, string $name) : Attribute;
    /**
     * Deletes a Attribute entity based on their user_id and name.
     *
     * @param int    $userId
     * @param string $name
     *
     * @return int
     */
    public function deleteOneByUserIdAndName(int $userId, string $name) : int;
}
