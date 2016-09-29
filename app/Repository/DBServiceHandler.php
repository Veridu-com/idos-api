<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\ServiceHandler;
use Illuminate\Support\Collection;

/**
 * Database-based ServiceHandler Repository Implementation.
 */
class DBServiceHandler extends AbstractSQLDBRepository implements ServiceHandlerInterface {
    /**
     * The table associated with the repository.
     *
     * @var string
     */
    protected $tableName = 'service_handlers';

    /**
     * The entity associated with the repository.
     *
     * @var string
     */
    protected $entityName = 'ServiceHandler';

    /**
     * Query attributes, used to fetch attributes from database.
     *
     * @var array
     */
    private $queryAttributes = [
        'service_handlers.*',
        'services.id as service.id',
        'services.name as service.name',
        'services.url as service.url',
        'services.public as service.public',
        'services.access as service.access',
        'services.enabled as service.enabled',
        'services.listens as service.listens',
        'services.triggers as service.triggers',
        'services.auth_username as service.auth_username',
        'services.auth_password as service.auth_password',
        'services.created_at as service.created_at',
        'services.updated_at as service.updated_at',
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

        'service' => [
            'type'       => 'MANY_TO_ONE',
            'table'      => 'services',
            'foreignKey' => 'service_id',
            'key'        => 'id',
            'entity'     => 'Service',
            'hydrate'    => [
                'id',
                'name',
                'url',
                'listens',
                'triggers',
                'enabled',
                'access',
                'created_at',
                'updated_at'
            ],
            'nullable' => false
        ],
    ];

    /**
     * {@inheritdoc}
     */
    public function findOne(int $companyId, int $serviceHandlerId) : ServiceHandler {
        return $this->findOneBy(
            [
            'id'         => $serviceHandlerId,
            'company_id' => $companyId
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function findAllFromService(int $companyId, string $serviceSlug) : Collection {
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
    public function findByCompanyId(int $companyId) : Collection {
        return $this->findBy(
            [
                'company_id' => $companyId
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getAllByCompanyId(int $companyId) : Collection {
        return $this->findBy(
            [
            'service.company_id' => $companyId
            ]
        );

        /*$array = $query
            ->join('services', 'services.id', '=', 'service_handlers.service_id')
            ->where('services.company_id', '=', $companyId)
            ->get($this->queryAttributes);

        return $this->castHydrate($array);*/
    }

    /**
     * {@inheritdoc}
     */
    public function deleteOne(int $companyId, int $serviceHandlerId) : int {
        return $this->deleteBy(
            [
                'company_id' => $companyId,
                'id'         => $serviceHandlerId
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
     */
    public function getAllByCompanyIdAndListener(int $companyId, string $event) {
        $collection = $this->query()
            ->join('services', 'services.id', 'service_id')
            ->where('service_handlers.company_id', $companyId)
            ->where('services.enabled', true)
            ->whereRaw('jsonb_exists(service_handlers.listens, ?)', [$event])
            ->get($this->queryAttributes);

        return $this->castHydrate($collection);
    }
}
