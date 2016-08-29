<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Digested;
use Illuminate\Support\Collection;

/**
 * Digested Repository Interface.
 */
interface DigestedInterface extends RepositoryInterface {
    /**
     * Gets all Digested entities based on their user_id and source_id.
     *
     * @param int $userId
     * @param int $sourceId
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllByUserIdAndSourceId(int $userId, int $sourceId) : Collection;
    /**
     * Gets all Digested entities based on their user_id, source_id and filtering them by name.
     *
     * @param int   $userId
     * @param int   $sourceId
     * @param array $names
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllByUserIdSourceIdAndNames(int $userId, int $sourceId, array $names) : Collection;
    /*
     * Deletes all Digested entities based on source_id.
     *
     * @param int $sourceId
     *
     * @return int
     */
    public function deleteBySourceId(int $sourceId) : int;
    /**
     * Find a Digested entity based on its user_id, source_id and name.
     *
     * @param int    $sourceId
     * @param string $name
     * 
     * @throws App\Exception\NotFound
     *
     * @return App\Entity\Digested
     */
    public function findOneByUserIdSourceIdAndName(int $userId, int $sourceId, string $name) : Digested;
    /**
     * Deletes a Digested entity based on their source_id and name.
     *
     * @param int    $sourceId
     * @param string $name
     *
     * @return int
     */
    public function deleteOneBySourceIdAndName(int $sourceId, string $name) : int;
}
