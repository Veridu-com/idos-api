<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Repository;

use App\Entity\CompanyDaemonHandler;
use Illuminate\Support\Collection;

/**
 * Database-based CompanyDaemonHandler Repository Implementation.
 */
class DBCompanyDaemonHandler extends AbstractDBRepository implements CompanyDaemonHandlerInterface {
    /**
     * The table associated with the repository.
     *
     * @var string
     */
    protected $tableName = 'company_daemon_handlers';
    /**
     * The entity associated with the repository.
     *
     * @var string
     */
    protected $entityName = 'CompanyDaemonHandler';

    /**
     * Used to select fields on join statements with the table daemon_handlers.
     *
     * @var array
     */
    private $querySelect = [
        'company_daemon_handlers.id',
        'company_daemon_handlers.updated_at',
        'company_daemon_handlers.created_at',
        'daemon_handlers.id as daemon_handler.id',
        'daemon_handlers.name as daemon_handler.name',
        'daemon_handlers.slug as daemon_handler.slug',
        'daemon_handlers.source as daemon_handler.source',
        'daemon_handlers.location as daemon_handler.location',
        'daemon_handlers.daemon_slug as daemon_handler.daemon_slug',
        'daemon_handlers.created_at as daemon_handler.created_at',
        'daemon_handlers.updated_at as daemon_handler.updated_at'
    ];

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
        $collection = new Collection($this->query()
            ->join('daemon_handlers', 'daemon_handlers.id', '=', 'company_daemon_handlers.daemon_handler_id')
            ->where('company_daemon_handlers.company_id', '=', $companyId)
            ->get($this->querySelect));

        return $this->castHydrate($collection);
    }

    /**
     * {@inheritdoc}
     */
    public function findOne(int $daemonHandlerId, int $companyId) : CompanyDaemonHandler {
        $entity = $this->query()
            ->join('daemon_handlers', 'daemon_handlers.id', '=', 'company_daemon_handlers.daemon_handler_id')
            ->where('daemon_handlers.id', '=', $daemonHandlerId)
            ->where('company_daemon_handlers.company_id', '=', $companyId)
            ->first($this->querySelect);

        if (! $entity) {
            throw new NotFound;
            
        }
        
        return $this->castHydrateEntity($entity);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteOne(int $id, int $companyId) : int {
        return $this->deleteBy([
            'id'            => $id,
            'company_id'    => $companyId
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByCompanyId(int $companyId) : int {
        return $this->deleteByKey('company_id', $companyId);
    }

}
