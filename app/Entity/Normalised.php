<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Entity;

/**
 * Normalised Entity.
 *
 * @apiEntity schema/normalised/normalisedEntity.json
 *
 * @property int    $id
 * @property string $name
 * @property string $value
 * @property int    $created_at
 * @property int    $updated_at
 */
class Normalised extends AbstractEntity {
    /**
     * {@inheritdoc}
     */
    protected $visible = ['name', 'value', 'created_at', 'updated_at'];
    /**
     * {@inheritdoc}
     */
    protected $dates = ['created_at', 'updated_at'];
    /**
     * {@inheritdoc}
     */
    protected $secure = ['value'];
}
