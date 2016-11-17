<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository\Metric;

use App\Entity\Metric\User;
use App\Entity\Identity;
use App\Repository\AbstractSQLDBRepository;
use Illuminate\Support\Collection;

/**
 * Database-based User Metric Repository Implementation.
 */
class DBUser extends AbstractSQLDBRepository implements UserInterface {
    /**
     * The table associated with the repository.
     *
     * @var string
     */
    protected $tableName = 'metrics_user';
    /**
     * The entity associated with the repository.
     *
     * @var string
     */
    protected $entityName = 'Metric\User';

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
        } else if ($from !== null) {
            $query = $query->where('created_at', '>=', date('Y-m-d H:i:s', $from));
        } else if ($to !== null) {
            $query = $query->where('created_at', '<=', date('Y-m-d H:i:s', $to));
        }

        if (isset($queryParams['source'])) {
            $sources = explode(',', $queryParams['source']);

            $query = $query->where(function ($query) use ($sources) {
                foreach ($sources as $source) {
                    if (!in_array($source, [
                        'amazon',
                        'dropbox',
                        'facebook',
                        'foursquare',
                        'google',
                        'instagram',
                        'linkedin',
                        'paypal',
                        'spotify',
                        'twitter',
                        'yahoo',
                        'email',
                        'sms',
                        'spotafriend',
                        'tracesmart',
                        'submitted'
                    ])
                    ) {
                        continue;
                    }

                    $query = $query->orWhereRaw('jsonb_exists(sources, \''. $source .'\')');
                }
            });
        }

        if (isset($queryParams['gate'])) {
            $gates = explode(',', $queryParams['gate']);

            $query = $query->where(function ($query) use ($gates) {
                foreach ($gates as $gate) {
                    if (preg_match('/[^a-zA-Z0-9-_.]/', $gate)) {
                        continue;
                    }

                    $query = $query->orWhereRaw('jsonb_exists(gates, \''. $gate .'\')');
                }
            });
        }

        if (isset($queryParams['flag'])) {
            $flags = explode(',', $queryParams['flag']);

            $query = $query->where(function ($query) use ($flags) {
                foreach ($flags as $flag) {
                    if (preg_match('/[^a-zA-Z0-9-_]/', $flag)) {
                        continue;
                    }

                    $query = $query->orWhereRaw('jsonb_exists(flags, \''. $flag .'\')');
                }
            });
        }

        foreach ($queryParams as $paramName => $paramValue) {
            switch ($paramName) {
                case 'birth_year':
                    if (substr_compare($paramValue, '>=', 0, 2) === 0) {
                        $value = (int) substr($paramValue, 2);
                        $query = $query->whereRaw('(data->>\'birthYear\')::int >= ?', [$value]);
                    } else if (substr_compare($paramValue, '<=', 0, 2) === 0) {
                        $value = (int) substr($paramValue, 2);
                        $query = $query->whereRaw('(data->>\'birthYear\')::int <= ?', [$value]);
                    } else if (substr_compare($paramValue, '>', 0, 1) === 0) {
                        $value = (int) substr($paramValue, 1);
                        $query = $query->whereRaw('(data->>\'birthYear\')::int > ?', [$value]);
                    } else if (substr_compare($paramValue, '<', 0, 1) === 0) {
                        $value = (int) substr($paramValue, 1);
                        $query = $query->whereRaw('(data->>\'birthYear\')::int < ?', [$value]);
                    } else {
                        $value = (int) $paramValue;
                        $query = $query->whereRaw('(data->>\'birthYear\')::int = ?', [$value]);
                    }
                    break;
            }
        }
        
        $entities = $query->get(['*']);

        return $entities;
    }
}
