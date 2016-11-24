<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Entity\Metric;

use App\Entity\AbstractEntity;

/**
 * User Metric Entity.
 *
 * @apiEntity schema/metric/userEntity.json
 *
 * @property int        $id
 * @property string     $hash
 * @property string     $sources
 * @property string     $data
 * @property string     $gates
 * @property string     $flags
 * @property int        $created_at
 * @property int        $updated_at
 */
class User extends AbstractEntity {
    /**
     * {@inheritdoc}
     */
    protected $visible = ['sources', 'data', 'gates', 'flags', 'count', 'created_at', 'updated_at'];
    /**
     * {@inheritdoc}
     */
    protected $dates = ['created_at', 'updated_at'];
    /**
     * {@inheritdoc}
     */
    protected $json = ['sources', 'data', 'gates', 'flags'];
}
