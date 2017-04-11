<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\Handler;
use Illuminate\Support\Collection;

/**
 * Database-based Handler Services Repository Implementation.
 */
class DBHandlerService extends AbstractSQLDBRepository implements HandlerServiceInterface {
    /**
     * The table associated with the repository.
     *
     * @var string
     */
    protected $tableName = 'handler_services';
    /**
     * The entity associated with the repository.
     *
     * @var string
     */
    protected $entityName = 'HandlerService';

    /**
     * {@inheritdoc}
     */
    protected $filterableKeys = [
        'name' => 'string'
    ];

    /**
     * Retrieves collection of Handler Services by companyId.
     *
     * @param int   $companyId   The company id
     * @param array $queryParams The query parameters
     *
     * @return \Illuminate\Support\Collection
     */
    public function getByServiceCompanyId(int $companyId, array $queryParams = []) : Collection {
        $query = $this->query()
            ->join('services', 'services.handler_service_id', 'handler_services.id')
            ->where('services.company_id', $companyId);

        $query = $this->filter($query, $queryParams);

        return $query->get(['handler_services.*']);
    }

    /**
     * Gets the by handler identifier.
     *
     * @param int   $handlerId   The handler identifier
     * @param array $queryParams The query parameters
     *
     * @return \Illuminate\Support\Collection
     */
    public function getByHandlerId(int $handlerId, array $queryParams = []) : Collection {
        return $this->findBy(
            [
            'handler_id' => $handlerId
            ], $queryParams
        );
    }

    /**
     * Retrieves collection of Handler Services by companyId.
     *
     * @param int   $companyId   The company id
     * @param array $queryParams The query parameters
     *
     * @return \Illuminate\Support\Collection
     */
    public function getByHandlerCompanyId(int $companyId, array $queryParams = []) : Collection {
        $query = $this->query()
            ->join('handlers', 'handlers.id', 'handler_services.handler_id')
            ->where('handlers.company_id', $companyId);

        $query = $this->filter($query, $queryParams);

        return $query->get(['handler_services.*']);
    }
}
