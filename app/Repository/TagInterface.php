<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Tag;
use Illuminate\Support\Collection;

/**
 * Tag Repository Interface.
 */
interface TagInterface extends RepositoryInterface {
    /**
     * Gets all Tags based on their User Id.
     *
     * @param int $userId
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllByUserId(int $userId) : Collection;
    /**
     * Gets all Tags based on their User Id.
     *
     * @param int   $userId
     * @param array $names
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllByUserIdAndTagNames(int $userId, array $names) : Collection;
    /*
     * Deletes all Tags based on their User Id.
     *
     * @param int $userId
     *
     * @return int
     */
    public function deleteByUserId(int $userId) : int;
    /**
     * Find a tag based on their userId and name.
     *
     * @param int    $userId
     * @param string $name
     * 
     * @throws App\Exception\NotFound
     *
     * @return App\Entity\Tag
     */
    public function findOneByUserIdAndName(int $userId, string $name) : Tag;
    /**
     * Deletes a tag based on their userId and name.
     *
     * @param int    $userId User id
     * @param string $name   Tag name
     *
     * @return int
     */
    public function deleteOneByUserIdAndName(int $userId, string $name) : int;
}
