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
 * @property int        $actor_id
 * @property int        $entity_id
 * @property string     $action
 * @property int        $created_at
 */
class Metric extends AbstractEntity {
    /**
     * {@inheritdoc}
     */
    protected $visible = ['actor_id', 'entity_id', 'action', 'count', 'created_at'];
    /**
     * {@inheritdoc}
     */
    protected $dates = ['created_at'];
}
