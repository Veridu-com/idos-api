<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Repository;

use App\Entity\CompanyServiceHandler;
use Illuminate\Support\Collection;

/**
 * Database-based CompanyServiceHandler Repository Implementation.
 */
class DBCompanyServiceHandler extends AbstractDBRepository implements CompanyServiceHandlerInterface {
    /**
     * The table associated with the repository.
     *
     * @var string
     */
    protected $tableName = 'company_service_handlers';
    /**
     * The entity associated with the repository.
     *
     * @var string
     */
    protected $entityName = 'CompanyServiceHandler';

    /**
     * Used to select fields on join statements with the table service_handlers.
     *
     * @var array
     */
    private $querySelect = [
        'company_service_handlers.id',
        'company_service_handlers.updated_at',
        'company_service_handlers.created_at',
        'service_handlers.id as service_handler.id',
        'service_handlers.name as service_handler.name',
        'service_handlers.slug as service_handler.slug',
        'service_handlers.source as service_handler.source',
        'service_handlers.location as service_handler.location',
        'service_handlers.service_slug as service_handler.service_slug',
        'service_handlers.created_at as service_handler.created_at',
        'service_handlers.updated_at as service_handler.updated_at'
    ];

    /**
     * {@inheritdoc}
     */
    public function findAllFromService(int $companyId, string $serviceSlug) : Collection {
        return $this->findBy([
            'company_id'    => $companyId,
            'service_slug'  => $serviceSlug,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllByCompanyId(int $companyId) : Collection {
        $collection = new Collection($this->query()
            ->join('service_handlers', 'service_handlers.id', '=', 'company_service_handlers.service_handler_id')
            ->where('company_service_handlers.company_id', '=', $companyId)
            ->get($this->querySelect));

        return $this->castHydrate($collection);
    }

    /**
     * {@inheritdoc}
     */
    public function findOne(int $id, int $companyId) : CompanyServiceHandler {
        $entity = $this->query()
            ->join('service_handlers', 'service_handlers.id', '=', 'company_service_handlers.service_handler_id')
            ->where('company_service_handlers.id', '=', $id)
            ->where('company_service_handlers.company_id', '=', $companyId)
            ->first($this->querySelect);

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
