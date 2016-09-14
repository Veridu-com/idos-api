<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Entity;

/**
 * Attribute Entity.
 *
 * @apiEntity schema/attribute/attributeEntity.json
 *
 * @property int    $id
 * @property string $name
 * @property string $value
 * @property int    $created_at
 * @property int    $updated_at
 */
class Attribute extends AbstractEntity {
    /**
     * {@inheritdoc}
     */
    protected $visible = ['creator', 'name', 'value', 'support', 'created_at'];

    /**
     * {@inheritdoc}
     */
    protected $dates = ['created_at'];

    /**
     * {@inheritdoc}
     */
    protected $secure = ['value'];

    /**
     * {@inheritdoc}
     */
    public $relationships = [
        'creator' => 'Service'
    ];
}
