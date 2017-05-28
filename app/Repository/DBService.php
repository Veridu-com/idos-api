<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\Service;
use Illuminate\Support\Collection;

/**
 * Database-based Service Repository Implementation.
 */
class DBService extends AbstractSQLDBRepository implements ServiceInterface {
    /**
     * The table associated with the repository.
     *
     * @var string
     */
    protected $tableName = 'services';
    /**
     * The entity associated with the repository.
     *
     * @var string
     */
    protected $entityName = 'Service';
    /**
     * Query attributes, used to fetch attributes from database.
     *
     * @var array
     */
    private $queryAttributes = [
        'services.*',
        'handler_services.id as handler_service.id',
        'handler_services.name as handler_service.name',
        'handler_services.url as handler_service.url',
        'handler_services.handler_id as handler_service.handler_id',
        'handler_services.enabled as handler_service.enabled',
        'handler_services.listens as handler_service.listens',
        'handler_services.created_at as handler_service.created_at',
        'handler_services.updated_at as handler_service.updated_at'
    ];
    /**
     * {@inheritdoc}
     */
    protected $filterableKeys = [
        /*'source.id' => 'decoded',
        'source.name' => 'string',
        'creator' => 'string',
        'name' => 'string',
        'type' => 'string',
        'created_at' => 'date'*/
    ];
    /**
     * {@inheritdoc}
     */
    protected $relationships = [
        'company' => [
            'type'       => 'MANY_TO_ONE',
            'table'      => 'companies',
            'foreignKey' => 'company_id',
            'key'        => 'id',
            'entity'     => 'Company',
            'hydrate'    => false,
            'nullable'   => false
        ],
        'handler_service' => [
            'type'       => 'MANY_TO_ONE',
            'table'      => 'handler_services',
            'foreignKey' => 'handler_service_id',
            'key'        => 'id',
            'entity'     => 'HandlerService',
            'hydrate'    => [
                'id',
                'name',
                'url',
                'handler_id',
                'listens',
                'enabled',
                'created_at',
                'updated_at'
            ],
            'nullable' => false
        ],
    ];

    /**
     * {@inheritdoc}
     */
    public function findOne(int $serviceId, int $companyId) : Service {
        return $this->findOneBy(
            [
                'id'         => $serviceId,
                'company_id' => $companyId
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getByService(int $companyId, string $serviceSlug) : Collection {
        return $this->findBy(
            [
                'company_id'   => $companyId,
                'service.slug' => $serviceSlug,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getByCompanyId(int $companyId) : Collection {
        return $this->findBy(
            [
                'company_id' => $companyId
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getByServiceCompanyId(int $companyId) : Collection {
        return $this->findBy(['service.company_id' => $companyId]);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteOne(int $companyId, int $serviceId) : int {
        return $this->deleteBy(
            [
                'company_id' => $companyId,
                'id'         => $serviceId
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByCompanyId(int $companyId) : int {
        return $this->deleteByKey('company_id', $companyId);
    }

    /**
     * Gets all by company identifier and listener.
     *
     * @param int    $companyId The company identifier
     * @param string $event     The event to look on "listens" column
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAllByCompanyIdAndListener(int $companyId, string $event) : Collection {
        $collection = $this->query()
            ->join('handler_services', 'handler_services.id', 'handler_service_id')
            ->where('services.company_id', $companyId)
            ->where('handler_services.enabled', true)
            ->whereRaw("handler_services.listens::text like '%$event%'")
            ->get($this->queryAttributes);

        return $this->castHydrate($collection);
    }
}
