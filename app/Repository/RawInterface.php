<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\Raw;
use App\Entity\Source;
use Illuminate\Support\Collection;

/**
 * Raw Repository Interface.
 */
interface RawInterface extends RepositoryInterface {
    /**
     * Gets all Raw entities based on Source.
     *
     * @param App\Entity\Source $source
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllBySource(Source $source) : Collection;

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
     * @param App\Entity\Source    $source
     * @param App\Entity\Raw $raw
     *
     * @throws App\Exception\NotFound
     *
     * @return App\Entity\Raw
     */
    public function create(Source $source, Raw $raw) : Raw;

    /**
     * Find a Raw entity based on its source and name.
     *
     * @param App\Entity\Source    $source
     * @param string $name
     *
     * @throws App\Exception\NotFound
     *
     * @return App\Entity\Raw
     */
    public function findOneBySourceAndName(Source $source, string $name) : Raw;

    /**
     * Update a Raw entity based on its source and name.
     *
     * @param App\Entity\Source    $source
     * @param string $name
     * @param string $data
     *
     * @throws App\Exception\NotFound
     *
     * @return App\Entity\Raw
     */
    public function updateOneBySourceAndName(Source $source, string $name, string $data) : Raw;
    
    /**
     * Deletes a Raw entity based on their source and name.
     *
     * @param App\Entity\Source    $source
     * @param string $name
     *
     * @return int
     */
    public function deleteOneBySourceAndName(Source $source, string $name) : int;
}
