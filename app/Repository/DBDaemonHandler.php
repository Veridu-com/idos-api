<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Repository;

use App\Entity\DaemonHandler;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;

/**
 * Database-based DaemonHandler Repository Implementation.
 */
class DBDaemonHandler extends AbstractDBRepository implements DaemonHandlerInterface {
    /**
     * The table associated with the repository.
     *
     * @var string
     */
    protected $tableName = 'daemon_handlers';
    /**
     * The entity associated with the repository.
     *
     * @var string
     */
    protected $entityName = 'DaemonHandler';


    /**
     * Used to select fields on join statements with the table daemon_handlers.
     *
     * @var array
     */
    private $querySelect = [
        'company_daemon_handlers.id as activated.id',
        'company_daemon_handlers.updated_at as activated.updated_at',
        'company_daemon_handlers.created_at as activated.created_at',
        'daemon_handlers.id',
        'daemon_handlers.name',
        'daemon_handlers.slug',
        'daemon_handlers.source',
        'daemon_handlers.location',
        'daemon_handlers.daemon_slug',
        'daemon_handlers.created_at',
        'daemon_handlers.updated_at'
    ];

    /**
     * Joins the current query object with the pivot table "company_daemon_handlers".
     *
     * @param      \Illuminate\Database\Query\Builder  $query      The query
     * @param      integer                             $companyId  The company identifier
     *
     * @return     \Illuminate\Database\Query\Builder
     */
    private function joinCompanyPivot(Builder $query, int $companyId) : Builder {
        return $query->leftJoin('company_daemon_handlers', function ($join) use ($companyId) {
                $join->on('company_daemon_handlers.daemon_handler_id', '=', 'daemon_handlers.id')
                    ->where('company_daemon_handlers.company_id', '=', $companyId);
            });
    }

    /**
     * {@inheritdoc}
     */
    public function findOne(int $companyId, int $daemonHandlerId) : DaemonHandler {
        $query = $this->joinCompanyPivot($this->query(), $companyId)
            ->where('daemon_handlers.id', '=', $daemonHandlerId);

        $entity = $query->first($this->querySelect);

        if (! $entity) {
            throw new NotFound;
        }

        return $this->castHydrateEntity($entity);
    }

    /**
     * {@inheritdoc}
     */
    public function findAllFromDaemon(int $companyId, string $daemonSlug) : Collection {
        return $this->findBy([
            'company_id'    => $companyId,
            'daemon_slug'  => $daemonSlug,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllByCompanyId(int $companyId) : Collection {
        return $this->findBy(['company_id' => $companyId]);
    }

    /**
     * Gets all with pivot table.
     *
     * @param      integer  $companyId    The company identifier
     * @param      array    $queryParams  The query parameters
     *
     * @return     \Illuminate\Support\Collection
     */
    public function getAllWithPivot(int $companyId, array $queryParams = []) : Collection {
        $query = $this->joinCompanyPivot($this->query(), $companyId);

        return $this->castHydrate(
            new Collection($this->filter($query, $queryParams)->get($this->querySelect))
        );
    }

    public function detach(int $relationCompanyId, int $daemonHandlerId) : int {
        return $this->query('company_daemon_handlers')
                ->where('company_id', $relationCompanyId)
                ->where('daemon_handler_id', $daemonHandlerId)
                ->delete();
    }

    public function attach(int $relationCompanyId, int $daemonHandlerId) : int {
        // inserts
        $id = $this->query('company_daemon_handlers')
                ->insertGetId([
                    'daemon_handler_id' => $daemonHandlerId,
                    'company_id' => $relationCompanyId
                 ]);

        return $id;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteOne(int $companyId, string $slug, string $daemonSlug) : int {
        return $this->deleteBy([
            'company_id'   => $companyId,
            'slug'         => $slug,
            'daemon_slug' => $daemonSlug
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByCompanyId(int $companyId) : int {
        return $this->deleteByKey('company_id', $companyId);
    }

}
