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
     * The entity associated with the repository.
     *
     * @var string
     */
    protected $entityName = 'Metric';

    /**
     * {@inheritdoc}
     */
    public function prepare($metricType = null) {
        $this->tableName = 'metrics' . ($metricType ? ('_' . $metricType) : '');
    }

    /**
     * {@inheritdoc}
     */
    public function getByDateInterval(int $from = null, int $to = null, array $queryParams = []) : Collection {
        $constraints = [];
        if ($from !== null && $to !== null) {
            $constraints['created_at'] = [[date('Y-m-d H:i:s', $from), date('Y-m-d H:i:s', $to)], 'BETWEEN'];
        } else if ($from !== null) {
            $constraints['created_at'] = [date('Y-m-d H:i:s', $from), '>='];
        } else if ($to !== null) {
            $constraints['created_at'] = [date('Y-m-d H:i:s', $to), '<='];
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
