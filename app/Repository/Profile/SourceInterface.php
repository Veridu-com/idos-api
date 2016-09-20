<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository\Profile;

use App\Entity\Profile\Source;
use Illuminate\Support\Collection;
use App\Repository\RepositoryInterface;

/**
 * Source Repository Interface.
 */
interface SourceInterface extends RepositoryInterface {
    /**
     * Finds one Source based on its Id and User Id.
     *
     * @param int $id
     * @param int $userId
     *
     * @return App\Entity\Profile\Source
     */
    public function findOne(int $id, int $userId) : Source;

    /**
     * Finds one Source based on its Name and User Id.
     *
     * @param string $name
     * @param int    $userId
     *
     * @return App\Entity\Source
     */
    public function findOneByName(string $name, int $userId) : Source;

    /**
     * Gets all Sources based on the User Id.
     *
     * @param int $userId
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllByUserId(int $userId) : Collection;

    /**
     * Deletes Sources based on the User Id.
     *
     * @param int $userId
     *
     * @return int
     */
    public function deleteByUserId(int $userId) : int;
}
