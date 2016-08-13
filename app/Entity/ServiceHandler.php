<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Entity;

use App\Extension\NameToSlugMutator;
use App\Extension\SecureFields;

/**
 * ServiceHandler's Entity.
 *
 * @apiEntity schema/service-handler/serviceHandlerEntity.json
 *
 * @property int        $id
 * @property int        $company_id
 * @property int        $service_slug
 * @property string     $name
 * @property string     $slug
 * @property string     $source
 * @property string     $location
 * @property string     $auth_username
 * @property string     $auth_password
 * @property int        $created_at
 * @property int        $updated_at
 */
class ServiceHandler extends AbstractEntity {
    use NameToSlugMutator;
    use SecureFields;

    /**
     * Cache prefix.
     */
    const CACHE_PREFIX = 'ServiceHandler';

    /**
     * {@inheritdoc}
     */
    protected $visible = ['id', 'listens', 'service', 'created_at', 'updated_at'];

    /**
     * {@inheritdoc}
     */
    protected $json = ['listens'];

    /**
     * {@inheritdoc}
     */
    public $relationships = [
        'service' => 'Service'
    ];

    /**
     * {@inheritdoc}
     */
    protected $dates = ['created_at', 'updated_at'];

    /**
     * The attributes that should be secured.
     *
     * @var array
     */
    protected $secure = ['auth_username', 'auth_password', 'location'];

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
