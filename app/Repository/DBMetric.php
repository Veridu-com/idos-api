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
 * Database-based Metric Repository Implementation.
 */
class DBMetric extends AbstractSQLDBRepository implements MetricInterface {
    /**
     * The table associated with the repository.
     *
     * @var string
     */
    protected $tableName = 'metrics';
    /**
     * The entity associated with the repository.
     *
     * @var string
     */
    protected $entityName = 'Metric';

    public function prepare($endpointName, $metricType = null) {
        $this->tableName = strtolower($endpointName) . '_metrics' . ($metricType ? (_ . $metricType) : '');
    }

    /**
     * {@inheritdoc}
     */
    public function get(array $queryParams = []) : Collection {
        return $this->findBy([], $queryParams);
    }
}
