<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Entity;

/**
 * CompanyServiceHandler's Entity.
 *
 * @apiEntity schema/service-handler/serviceHandlerEntity.json
 *
 * @property int        $id
 * @property int        $company_id
 * @property int        $service_handler_id
 * @property int        $created_at
 * @property int        $updated_at
 */
class CompanyServiceHandler extends AbstractEntity {
    /**
     * Cache prefix.
     */
    const CACHE_PREFIX = 'CompanyServiceHandler';

    /**
     * {@inheritdoc}
     */
    protected $visible = ['id', 'service_handler', 'created_at', 'updated_at'];

    /**
     * {@inheritdoc}
     */
    protected $dates = ['created_at', 'updated_at'];

    /**
     * {@inheritdoc}
     */
    public $relationships = [
        'service_handler' => 'ServiceHandler'
    ];

    /**
     * {@inheritdoc}
     */
    public function getCacheKeys() : array {
        return [
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getReferenceCacheKeys() : array {
        return array_merge([
        ],
        $this->getCacheKeys());
    }
}
