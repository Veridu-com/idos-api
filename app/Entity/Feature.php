<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Entity;

use App\Extension\SecureFields;
use App\Helper\Utils;

/**
 * Features Entity.
 *
 * @apiEntity schema/user/featureEntity.json
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
    use SecureFields;

    /**
     * {@inheritdoc}
     */
    protected $visible = ['user_id', 'source', 'name', 'creator', 'type', 'value', 'created_at', 'updated_at'];
    /**
     * {@inheritdoc}
     */
    protected $dates = ['created_at', 'updated_at'];
    /**
     * The attributes that should be secured.
     *
     * @var array
     */
    protected $secure = ['value'];

    /**
     * {@inheritdoc}
     */
    public $relationships = [
        'source' => 'Source'
    ];

    public function getValueAttribute() {
        if ($this->attributes['type'] === 'integer') {
            return (int) $this->attributes['value'];
        }

        if ($this->attributes['type'] === 'boolean') {
            return (bool) $this->attributes['value'];
        }

        if ($this->attributes['type'] === 'double') {
            return (double) $this->attributes['value'];
        }

        return $this->attributes['value'];
    }
}
