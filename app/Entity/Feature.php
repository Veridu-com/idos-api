<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Entity;

/**
 * Features Entity.
 *
 * @apiEntity schema/feature/featureEntity.json
 *
 * @property int    $id
 * @property int    $user_id
 * @property string $name
 * @property string $creator
 * @property string $type
 * @property string $value
 * @property int    $user_id
 * @property int    $created_at
 * @property int    $updated_at
 */
class Feature extends AbstractEntity {
    /**
     * {@inheritdoc}
     */
    protected $visible = [
        'id',
        'source',
        'name',
        'creator',
        'type',
        'value',
        'created_at',
        'updated_at'
    ];
    /**
     * {@inheritdoc}
     */
    protected $dates = ['created_at', 'updated_at'];
    /**
     * {@inheritdoc}
     */
    protected $secure = ['value'];
    /**
     * {@inheritdoc}
     */
    public $relationships = [
        'source'  => 'Source',
        'creator' => 'Service'
    ];

    public function getValueAttribute($value) {
        if ($this->attributes['type'] === 'integer') {
            return (int) $value;
        }

        if ($this->attributes['type'] === 'boolean') {
            return (bool) $value;
        }

        if ($this->attributes['type'] === 'double') {
            return (double) $value;
        }

        return $value;
    }
}
