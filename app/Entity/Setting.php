<?php

/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Entity;

/**
 * Settings Entity.
 *
 * @apiEntity schema/setting/settingEntity.json
 *
 * @property int 	$id
 * @property int 	$company_id
 * @property string $section
 * @property string $property
 * @property string $value
 * @property int    $created_at
 * @property int    $updated_at
 */
class Setting extends AbstractEntity {
    /**
     * Cache prefix.
     */
    const CACHE_PREFIX = 'Setting';

    /**
     * {@inheritdoc}
     */
    protected $visible = ['section', 'property', 'value', 'created_at', 'updated_at'];

    /**
     * {@inheritdoc}
     */
    protected $dates = ['created_at', 'updated_at'];

    public function getValueAttribute($value) {
        return  is_string($value) ? $value : stream_get_contents($value, -1, 0);
    }

    public function setValueAttribute($value) {
        $value                     = is_string($value) ? $value : stream_get_contents($value, -1, 0);
        $this->attributes['value'] = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheKeys() : array {
        return [
            sprintf('%s.company_id.%s.section.%s.property.%s', self::CACHE_PREFIX, $this->companyId, $this->section, $this->property)
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getReferenceCacheKeys() : array {
        return array_merge([
            sprintf('%s.by.company_id.%s', self::CACHE_PREFIX, $this->companyId),
            sprintf('%s.by.company_id.%s.section.%s', self::CACHE_PREFIX, $this->companyId, $this->section)
        ],
        $this->getCacheKeys());
    }
}
