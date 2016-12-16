<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Entity\Profile;

use App\Entity\AbstractEntity;
use App\Helper\Utils;

/**
 * Gates Entity.
 *
 * @apiEntity schema/gate/gateEntity.json
 *
 * @property int    $id
 * @property string $name
 * @property string $slug
 * @property string $confidenceLevel
 * @property int    $user_id
 * @property int    $created_at
 * @property int    $updated_at
 */
class Gate extends AbstractEntity {
    /**
     * {@inheritdoc}
     */
    protected $visible = [
        'id',
        'name',
        'slug',
        'confidence_level',
        'review',
        'creator',
        'category',
        'created_at',
        'updated_at'
    ];

    /**
     * Sets the name attribute.
     *
     * @param string $value The value
     *
     * @return self
     */
    public function setNameAttribute(string $value) : self {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Utils::slugify($value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected $dates = ['created_at', 'updated_at'];

    /**
     * {@inheritdoc}
     */
    protected $obfuscated = ['id', 'creator'];
    /**
     * {@inheritdoc}
     */
    public $relationships = [
        'creator' => 'Handler',
        'category' => 'Category'
    ];
}
