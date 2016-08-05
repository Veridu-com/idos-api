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
    public function findOne(int $companyId, string $slug, string $serviceSlug) : ServiceHandler {
        return $this->findOneBy([
            'company_id'    => $companyId,
            'slug'          => $slug,
            'service_slug'  => $serviceSlug,
        ]);
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
        return $this->findBy(['company_id' => $companyId]);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteOne(int $companyId, string $slug, string $serviceSlug) : int {
        return $this->deleteBy([
            'company_id'   => $companyId,
            'slug'         => $slug,
            'service_slug' => $serviceSlug
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByCompanyId(int $companyId) : int {
        return $this->deleteByKey('company_id', $companyId);
    }

}
