<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Entity\Profile;

use App\Entity\AbstractEntity;

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
    // protected $json = ['value'];
    /**
     * {@inheritdoc}
     */
    protected $secure = ['value'];
    /**
     * {@inheritdoc}
     */
    public $relationships = [
        'creator' => 'Handler'
    ];

    public function getValueAttribute($value) {
        switch ($this->attributes['type']) {
            case 'integer':
                return (int) $value;
            case 'float':
            case 'double':
            case 'real':
                return (float) $value;
            case 'boolean':
                return (bool) $value;
            case 'array':
                if (is_array($value)) {
                    return $value;
                }

                return json_decode($value, true);
        }

        return $value;
    }
}
