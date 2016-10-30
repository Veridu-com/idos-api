<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Entity;

/**
 * Metric Entity.
 *
 * @apiEntity schema/metric/metricEntity.json
 *
 * @property int        $id
 * @property string     $name
 * @property float      $value
 * @property int        $created_at
 */
class Metric extends AbstractEntity {
    /**
     * {@inheritdoc}
     */
    protected $visible = ['name', 'value', 'created_at'];
    /**
     * {@inheritdoc}
     */
    protected $dates = ['created_at'];
}
