<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Entity;

/**
 * Source Metric Entity.
 *
 * @apiEntity schema/metric/metricEntity.json
 *
 * @property int        $id
 * @property int        $credential_id
 * @property string     $provider
 * @property boolean    $sso
 * @property string     $action
 * @property string     $count
 * @property int        $created_at
 */
class SourceMetric extends AbstractEntity {
    /**
     * {@inheritdoc}
     */
    protected $visible = ['credential_id', 'provider', 'sso', 'action', 'count', 'created_at'];
    /**
     * {@inheritdoc}
     */
    protected $dates = ['created_at'];
}
