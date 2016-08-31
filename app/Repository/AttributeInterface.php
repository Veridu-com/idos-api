<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Attribute;
use Illuminate\Support\Collection;

/**
 * Attribute Repository Interface.
 */
interface AttributeInterface extends RepositoryInterface {
    /**
     * Gets all Attribute entities based on their user_id.
     *
     * @param int $userId
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllByUserId(int $userId) : Collection;
    /**
     * Gets all Attribute entities based on their user_id, filtering them by name.
     *
     * @param int   $userId
     * @param array $names
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllByUserIdAndNames(int $userId, array $names) : Collection;
    /*
     * Deletes all Attribute entities based on user_id.
     *
     * @param int $userId
     *
     * @return int
     */
    public function deleteByUserId(int $userId) : int;
    /**
     * Find a Attribute entity based on its user_id and name.
     *
     * @param int    $userId
     * @param string $name
     * 
     * @throws App\Exception\NotFound
     *
     * @return App\Entity\Attribute
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
