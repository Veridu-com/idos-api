<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository\Profile;

use App\Entity\Profile\Source;
use App\Repository\RepositoryInterface;
use Illuminate\Support\Collection;

/**
 * Source Repository Interface.
 */
interface SourceInterface extends RepositoryInterface {
    /**
     * Returns a source based on its id and user id.
     *
     * @param int $id
     * @param int $userId
     *
     * @return \App\Entity\Profile\Source
     */
    public function findOne(int $id, int $userId) : Source;

    /**
     * Return a source based on its name and user id.
     *
     * @param string $name
     * @param int    $userId
     *
     * @return \App\Entity\Profile\Source
     */
    public function findOneByName(string $name, int $userId) : Source;

    /**
     * Return sources based on their user id.
     *
     * @param int $userId
     *
     * @return \Illuminate\Support\Collection
     */
    public function getByUserId(int $userId) : Collection;

    /**
     * Return sources based on their user id and a set of filters.
     *
     * @param int   $userId
     * @param array $filters
     *
     * @return \Illuminate\Support\Collection
     */
    public function getByUserIdFiltered(int $userId, array $filters = []) : Collection;

    /**
     * Return sources based on their user id and name.
     *
     * @param int    $userId
     * @param string $name
     *
     * @return \Illuminate\Support\Collection
     */
    public function getByUserIdAndName(int $userId, string $name) : Collection;

    /**
     * Gets the latest sources of the user.
     *
     * @param int $userId The user identifier
     *
     * @return Collection The latest sources.
     */
    public function getLatest(int $userId) : Collection;

    /**
     * Deletes sources based on the user id.
     *
     * @param int $userId
     *
     * @return int
     */
    public function deleteByUserId(int $userId) : int;
}
