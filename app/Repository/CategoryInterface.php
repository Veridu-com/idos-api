<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use Illuminate\Support\Collection;

/**
 * Category Repository Interface.
 */
interface CategoryInterface extends RepositoryInterface {
    /**
     * Return all categories.
     *
     * @param array $queryParams
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAll(array $queryParams = []) : Collection;
}
