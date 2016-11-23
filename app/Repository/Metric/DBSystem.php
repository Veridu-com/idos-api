<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository\Metric;

use App\Entity\Metric\System;
use App\Entity\Identity;
use App\Repository\AbstractSQLDBRepository;
use Illuminate\Support\Collection;

/**
 * Database-based System Metric Repository Implementation.
 */
class DBSystem extends AbstractSQLDBRepository implements SystemInterface {
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
    protected $entityName = 'Metric\System';

    /**
     * {@inheritdoc}
     */
    public function prepare($metricType = null) {
        switch ($metricType) {
            case 'hourly':
                $this->tableName = 'metrics_hourly';
                break;
            case 'daily':
                $this->tableName = 'metrics_daily';
                break;
            default:
                $this->tableName = 'metrics';
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getByIdentityAndDateInterval(Identity $identity, int $from = null, int $to = null, array $queryParams = []) : Collection {
        $credentials = $this
            ->query('credentials', 'App\Entity\Company\Credential')
            ->join('members', 'members.company_id', '=', 'credentials.company_id')
            ->where('members.identity_id', '=', $identity->id)
            ->get(['credentials.public']);

        $allowedKeys = [];
        foreach ($credentials as $credential) {
            $allowedKeys[] = $credential->public;
        }

        $keys = $allowedKeys;
        if (isset($queryParams['credential_public'])) {
            $keys = explode(',', $queryParams['credential_public']);
        }

        if (count(array_diff($keys, $allowedKeys)) > 0) {
            return collect();
        }

        $query = $this
            ->query()
            ->whereIn('credential_public', $keys);

        if ($from !== null && $to !== null) {
            $query = $query->whereBetween('created_at', [date('Y-m-d H:i:s', $from), date('Y-m-d H:i:s', $to)]);
        } elseif ($from !== null) {
            $query = $query->where('created_at', '>=', date('Y-m-d H:i:s', $from));
        } elseif ($to !== null) {
            $query = $query->where('created_at', '<=', date('Y-m-d H:i:s', $to));
        }

        $entities = $query->get(['*']);
        foreach ($entities as $entity) {
            if (! $entity->count) {
                $entity->count = 1;
            }
        }

        return $entities;
    }
}
