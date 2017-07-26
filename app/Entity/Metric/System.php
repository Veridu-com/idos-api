<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Entity\Metric;

use App\Entity\AbstractEntity;

/**
 * System Metric Entity.
 *
 * @apiEntity schema/metric/systemEntity.json
 *
 * @property int        $id
 * @property string     $endpoint
 * @property string     $action
 * @property int        $count
 * @property string     $data
 * @property int        $created_at
 */
class System extends AbstractEntity {
    /**
     * {@inheritdoc}
     */
    protected $visible = ['endpoint', 'action', 'count', 'data', 'created_at'];
    /**
     * {@inheritdoc}
     */
    protected $dates = ['created_at'];
    /**
     * {@inheritdoc}
     */
    protected $json = ['data'];
}
