<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\EntityInterface;
use App\Entity\Raw;
use App\Entity\Source;
use Illuminate\Support\Collection;

/**
 * Raw Repository Interface.
 */
interface RawInterface extends RepositoryInterface {
    /**
     * Gets all Raw entities based on Source, possibly filtering by name.
     *
     * @param App\Entity\Source $source
     * @param array             $collections
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllBySourceAndCollections(Source $source, array $collectionNames = []) : Collection;

    /*
     * Deletes all Raw entities based on source.
     *
     * @param App\Entity\Source $source
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
     * @return App\Entity\Raw
     */
    public function create(array $attributes) : EntityInterface;

    /**
     * Find a Raw entity based on its source and name.
     *
     * @param App\Entity\Source $source
     * @param string            $name
     *
     * @throws App\Exception\NotFound
     *
     * @return App\Entity\Raw
     */
    public function findOneBySourceAndCollection(Source $source, string $collection) : Raw;

    /**
     * Update a Raw entity based on its source and name.
     *
     * @param App\Entity\Source $source
     * @param string            $name
     * @param string            $data
     *
     * @throws App\Exception\NotFound
     *
     * @return App\Entity\Raw
     */
    public function updateOneBySourceAndCollection(Source $source, string $collection, string $data) : Raw;

    /**
     * Deletes a Raw entity based on their source and name.
     *
     * @param App\Entity\Source $source
     * @param string            $name
     *
     * @return int
     */
    public function deleteOneBySourceAndCollection(Source $source, string $collection) : int;
}
