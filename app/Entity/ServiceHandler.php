<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Entity;

use App\Extension\SecureFields;

/**
 * ServiceHandler's Entity.
 *
 * @apiEntity schema/setting/settingEntity.json
 *
 * @property int        $id
 * @property int        $company_id
 * @property int        $service_id
 * @property string     $name
 * @property string     $slug
 * @property string     $source
 * @property string     $location
 * @property string     $auth_username
 * @property string     $auth_password
 */
class ServiceHandler extends AbstractEntity {    
    /**
     * Cache prefix.
     */
    const CACHE_PREFIX = 'ServiceHandler';

    /**
     * {@inheritdoc}
     */
    protected $visible = ['name', 'slug', 'source', 'location', 'created_at', 'updated_at'];

    /**
     * {@inheritdoc}
     */
    protected $dates = ['created_at', 'updated_at'];

    /**
     * {@inheritdoc}
     */
    public static $relationships = [
        'service' => 'Service'
    ];

    /**
     * Property mutator (getter) for $value.
     *
     * @param mixed $value
     *
     * @return string
     */
    public function getValueAttribute($value) : string {
        return  is_string($value) ? $value : stream_get_contents($value, -1, 0);
    }

    /**
     * Property mutator (setter) for $value.
     *
     * @param mixed $value
     *
     * @return App\Entity\ServiceHandler
     */
    public function setValueAttribute($value) : self {
        $value                     = is_string($value) ? $value : stream_get_contents($value, -1, 0);
        $this->attributes['value'] = $value;

        return $this;
    }

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
