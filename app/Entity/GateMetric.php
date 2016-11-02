<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Entity;

/**
 * Gate Metric Entity.
 *
 * @apiEntity schema/metric/metricEntity.json
 *
 * @property int        $id
 * @property int        $credential_id
 * @property string     $name
 * @property boolean    $pass
 * @property string     $action
 * @property string     $count
 * @property int        $created_at
 */
class GateMetric extends AbstractEntity {
    /**
     * {@inheritdoc}
     */
    protected $visible = ['credential_id', 'name', 'pass', 'action', 'count', 'created_at'];
    /**
     * {@inheritdoc}
     */
    protected $dates = ['created_at'];
}
