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
     * Prepare the repository to respond accordingly to an specific metric entity.
     *
     * @param string       $entityName  The entity name
     * @param string|null  $metricType  The metric type
     */
    public function prepare($entityName, $metricType = null) {
        $this->tableName = strtolower($entityName) . '_metrics' . ($metricType ? ('_' . $metricType) : '');
        $this->entityName = $entityName . 'Metric';
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $from = null, string $to = null, array $queryParams = []) : Collection {
        $constraints = [];
        if ($from !== null) {
            $constraints['created_at'] = [$from, '>='];
        }

        if ($to !== null) {
            $constraints['created_at'] = [$to, '<='];
        }

        $entities = $this->findBy($constraints, $queryParams);
        foreach ($entities as $entity) {
            if (! $entity->count) {
                $entity->count = 1;
            }
        }

        return $entities;
    }
}
