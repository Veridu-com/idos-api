<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository\Profile;

use App\Entity\Profile\Tag;
use App\Repository\RepositoryInterface;
use Illuminate\Support\Collection;

/**
 * Tag Repository Interface.
 */
interface TagInterface extends RepositoryInterface {
    /**
     * Returns a tag based on their user id and name.
     *
     * @param string $slug
     * @param int    $userId
     *
     * @return Tag
     */
    public function findOne(string $slug, int $userId) : Tag;

    /**
     * Return tags based on their user id.
     *
     * @param int   $userId
     * @param array $queryParams
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByUserId(int $userId, array $queryParams = []) : Collection;

    /**
     * Deletes a tag based on their user id and name.
     *
     * @param int    $userId User id
     * @param string $name   Tag name
     *
     * @return int
     */
    public function deleteOneByUserIdAndSlug(int $userId, string $name) : int;

    /*
     * Delete tags based on their user id.
     *
     * @param int $userId
     *
     * @return int
     */
    public function deleteByUserId(int $userId) : int;
}
