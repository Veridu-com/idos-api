<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\Company;
use App\Entity\Handler;
use App\Exception\NotFound;
use Illuminate\Database\Query\Builder;
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
     * {@inheritdoc}
     */
    public function getByCompanyId(int $companyId, array $queryParams) : Collection {
        return $this->findBy([
            'company_id' => $companyId
        ], $queryParams);
    }

    /**
     * Gets the by handler identifier.
     *
     * @param      integer  $handlerId    The handler identifier
     * @param      array    $queryParams  The query parameters
     *
     * @return \Illuminate\Support\Collection
     */
    public function getByHandlerId(int $handlerId, array $queryParams) : Collection {
        return $this->findBy([
            'handler_id' => $handlerId
        ], $queryParams);
    }
}
