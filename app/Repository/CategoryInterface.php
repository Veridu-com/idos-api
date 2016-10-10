<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\Category;
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
     * @return Collection
     */
    public function getAll(array $queryParams = []) : Collection;
}
