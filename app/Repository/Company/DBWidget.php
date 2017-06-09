<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository\Company;

use App\Entity\Company\Widget;
use App\Repository\AbstractDBRepository;
use Illuminate\Support\Collection;

/**
 * Database-based Widget Repository Implementation.
 */
class DBWidget extends AbstractDBRepository implements WidgetInterface {
    /**
     * The table associated with the repository.
     *
     * @var string
     */
    protected $tableName = 'widgets';
    /**
     * The entity associated with the repository.
     *
     * @var string
     */
    protected $entityName = 'Company\Widget';
    /**
     * {@inheritdoc}
     */
    protected $relationships = [
        'credential' => [
            'type'       => 'MANY_TO_ONE',
            'table'      => 'credentials',
            'foreignKey' => 'credential_id',
            'key'        => 'id',
            'entity'     => 'Credential',
            'nullable'   => false,
            'hydrate'    => ['name', 'slug', 'public', 'production', 'created_at', 'updated_at']
        ]
    ];

    /**
     * {@inheritdoc}
     */
    public function getByCompanyId(int $companyId) : Collection {
        return $this->findBy(['company_id' => $companyId]);
    }

    /**
     * {@inheritdoc}
     */
    public function findByHash(string $hash) : Widget {
        return $this->findOneBy(['hash' => $hash]);
    }
}
