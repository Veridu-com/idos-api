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
     * Prepare the repository to respond accordingly to an specific metric entity.
     *
     * @param string|null  $metricType  The metric type
     */
    public function prepare($metricType = null);

    /**
     * Return logged events.
     *
     * @param array $queryParams
     *
     * @return \Illuminate\Support\Collection
     */
    public function getByDateInterval(int $from, int $to, array $queryParams = []) : Collection;
}
