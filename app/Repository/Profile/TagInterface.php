<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository\Profile;

use App\Entity\Profile\Tag;
use Illuminate\Support\Collection;
use App\Repository\RepositoryInterface;

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
     * @param array $queryParams
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllByUserIdAndTagSlugs(int $userId, array $queryParams = []) : Collection;
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
     * @return App\Entity\Profile\Tag
     */
    public function findOneByUserIdAndSlug(int $userId, string $name) : Tag;
    /**
     * Deletes a tag based on their userId and name.
     *
     * @param int    $userId User id
     * @param string $name   Tag name
     *
     * @return int
     */
    public function deleteOneByUserIdAndSlug(int $userId, string $name) : int;
}
