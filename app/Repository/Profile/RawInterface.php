<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository\Profile;

use App\Entity\EntityInterface;
use App\Entity\Profile\Raw;
use App\Entity\Profile\Source;
use App\Repository\RepositoryInterface;
use Illuminate\Support\Collection;

/**
 * Raw Repository Interface.
 */
interface RawInterface extends RepositoryInterface {
    public function findByUserId(int $userId, array $queryParams = []) : Collection;
    public function findOne(Source $source, string $collection) : Raw;
    /*
     * Deletes all Raw entities based on source.
     *
     * @param App\Entity\Profile\Source $source
     *
     * @return int
     */
    public function deleteBySource(Source $source) : int;

    /**
     * Creates a new Raw entity.
     *
     * @param array $attributes
     *
     * @throws App\Exception\NotFound
     *
     * @return App\Entity\Profile\Raw
     */
    public function create(array $attributes) : EntityInterface;

    /**
     * Find a Raw entity based on its source and name.
     *
     * @param App\Entity\Profile\Source $source
     * @param string                    $name
     *
     * @throws App\Exception\NotFound
     *
     * @return App\Entity\Profile\Raw
     */
    public function findOneBySourceAndCollection(Source $source, string $collection) : Raw;

    /**
     * Update a Raw entity based on its source and name.
     *
     * @param App\Entity\Profile\Source $source
     * @param string                    $name
     * @param string                    $data
     *
     * @throws App\Exception\NotFound
     *
     * @return App\Entity\Profile\Raw
     */
    public function updateOneBySourceAndCollection(Source $source, string $collection, string $data) : Raw;

    /**
     * Deletes a Raw entity based on their source and name.
     *
     * @param App\Entity\Profile\Source $source
     * @param string                    $name
     *
     * @return int
     */
    public function deleteOneBySourceAndCollection(Source $source, string $collection) : int;
}
