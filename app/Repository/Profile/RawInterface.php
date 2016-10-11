<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository\Profile;

use App\Entity\Profile\Raw;
use App\Entity\Profile\Source;
use App\Repository\RepositoryInterface;
use Illuminate\Support\Collection;

/**
 * Raw Repository Interface.
 */
interface RawInterface extends RepositoryInterface {
    /**
     * Returns a raw data based on its source and collection.
     *
     * @param string $collection The service id
     * @param Source $source     The source entity
     *
     * @return Raw
     */
    public function findOne(string $collection, Source $source) : Raw;

    /**
     * Find a Raw entity based on its source and name.
     *
     * @param string                           $collection
     * @param Source|App\Entity\Profile\Source $source
     *
     * @return Raw|App\Entity\Profile\Raw
     *
     * @internal param string $name
     */
    public function findOneBySourceAndCollection(string $collection, Source $source) : Raw;

    /**
     * Return raw data based on their user id.
     *
     * @param int   $userId
     * @param array $queryParams
     *
     * @return Collection
     */
    public function getByUserId(int $userId, array $queryParams = []) : Collection;

    /**
     * Update a raw data based on its source and name.
     *
     * @param string                           $collection
     * @param Source|App\Entity\Profile\Source $source
     * @param string                           $data
     *
     * @return Raw|App\Entity\Profile\Raw
     */
    public function updateOneBySourceAndCollection(string $collection, Source $source, string $data) : Raw;

    /**
     * Deletes a raw data based on their source and collection.
     *
     * @param string                           $collection
     * @param Source|App\Entity\Profile\Source $source
     *
     * @return int
     *
     * @internal param string $name
     */
    public function deleteOneBySourceAndCollection(string $collection, Source $source) : int;

    /*
     * Delete raw data based on source.
     *
     * @param App\Entity\Profile\Source $source
     *
     * @return int
     */
    public function deleteBySource(Source $source) : int;
}
