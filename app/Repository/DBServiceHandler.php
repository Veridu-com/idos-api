<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ServiceHandler;
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
     * {@inheritdoc}
     */
    public function findOne(int $companyId, string $serviceSlug, string $serviceHandlerSlug) : ServiceHandler {
        return $this->query()
            ->join('services', sprintf('%s.service_id', $this->tableName), '=', 'services.id')
            ->where('services.slug', '=', $serviceSlug)
            ->where(sprintf('%s.company_id', $this->tableName), '=', $companyId)
            ->where(sprintf('%s.slug', $this->tableName), '=',$serviceHandlerSlug)
            ->first();
    }

    /**
     * {@inheritdoc}
     */
    public function findAllFromService(int $companyId, string $serviceSlug) : Collection {
        $items =  new Collection($this->query()
            ->join('services', sprintf('%s.service_id', $this->tableName), '=', 'services.id')
            ->where('services.slug', '=', $serviceSlug)
            ->where(sprintf('%s.company_id', $this->tableName), '=', $companyId)
            ->get([
                'services.name as service.name',
                'services.slug as service.slug',
                'services.enabled as service.enabled',
                'service_handlers.*',
            ])
        );

        return $this->castHydrate($items);
    }

    /**
     * {@inheritdoc}
     */
    public function update(ServiceHandler &$entity) : int {
        $serialized = $entity->serialize();

        return $this->query()
            ->where('company_id', $entity->company_id)
            ->where('service', $entity->service)
            ->where('property', $entity->property)
            ->update($serialized);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllByCompanyId(int $companyId) : Collection {
        return $this->findBy(['company_id' => $companyId]);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllByCompanyIdAndSection(int $companyId, string $service) : Collection {
        return $this->findBy([
            'company_id' => $companyId,
            'service'    => $service
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteOne(int $companyId, string $service, string $property) : int {
        return $this->query()
            ->where('company_id', $companyId)
            ->where('service', $service)
            ->where('property', $property)
            ->delete();
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByCompanyId(int $companyId) : int {
        return $this->deleteByKey('company_id', $companyId);
    }

}
