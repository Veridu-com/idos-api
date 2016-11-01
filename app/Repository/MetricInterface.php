<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\Metric;
use Illuminate\Support\Collection;

/**
 * Logged Event Repository Interface.
 */
interface MetricInterface extends RepositoryInterface {
    /**
     * Return logged events.
     *
     * @param array $queryParams
     *
     * @return \Illuminate\Support\Collection
     */
    public function get(array $queryParams) : Collection;
}
