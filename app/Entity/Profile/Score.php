<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Entity\Profile;

use App\Entity\AbstractEntity;

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
    protected $visible = ['creator', 'attribute', 'name', 'value', 'created_at', 'updated_at'];

    /**
     * {@inheritdoc}
     */
    protected $dates = ['created_at'];

    /**
     * {@inheritdoc}
     */
    public $relationships = [
        'creator' => 'Service'
    ];

    /**
     * Property Acessor for $value.
     *
     * @return float
     */
    public function getValueAttribute($value) : float {
        return floatval($value);
    }
}
