<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ServiceHandler;
use App\Exception\NotFound;
use Illuminate\Support\Collection;

/**
 * Database-based ServiceHandler Repository Implementation.
 */
class DBServiceHandler extends AbstractDBRepository implements ServiceHandlerInterface {
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
    protected $queryAttributes = [
        'service_handlers.*',
        'services.id as service.id',
        'services.name as service.name',
        'services.url as service.url',
        'services.listens as service.listens',
        'services.triggers as service.triggers',
        'services.enabled as service.enabled',
        'services.created_at as service.created_at',
        'services.updated_at as service.updated_at',
    ];

    /**
     * {@inheritdoc}
     */
    public function findOne(int $companyId, int $serviceHandlerId) : ServiceHandler {
        $entity = $this->query()
                    ->join('services', 'services.id', '=', 'service_handlers.service_id')
                    ->where('service_handlers.id', $serviceHandlerId)
                    ->where('service_handlers.company_id', $companyId)
                    ->first($this->queryAttributes);

        if (! $entity) {
            throw new NotFound();
        }

        return $this->castHydrateEntity($entity);
    }

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
        $query = $this->query();

        $array = $query
                    ->join('services', 'services.id', '=', 'service_handlers.service_id')
                    ->get($this->queryAttributes);

        return $this->castHydrate(new Collection($array));
    }

    /**
     * {@inheritdoc}
     */
    public function deleteOne(int $companyId, int $serviceHandlerId) : int {
        return $this->deleteBy([
            'company_id'   => $companyId,
            'id'           => $serviceHandlerId
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByCompanyId(int $companyId) : int {
        return $this->deleteByKey('company_id', $companyId);
    }

}
