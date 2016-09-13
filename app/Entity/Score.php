<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Entity;

/**
 * Score Entity.
 *
 * @apiEntity schema/score/scoreEntity.json
 *
 * @property int    $id
 * @property string $name
 * @property string $value
 * @property int    $created_at
 * @property int    $updated_at
 */
class Score extends AbstractEntity {
    /**
     * {@inheritdoc}
     */
    protected $visible = ['name', 'value', 'created_at'];
    /**
     * {@inheritdoc}
     */
    protected $dates = ['created_at'];
    /**
     * The attributes that should be secured.
     *
     * @var array
     */

    /**
     * Property Acessor for $value.
     *
     * @return float
     */
    public function getValueAttribute() : float {
        return floatval($this->attributes['value']);
    }
}
