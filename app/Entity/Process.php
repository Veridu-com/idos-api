<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Entity;

/**
 * Process Entity.
 *
 * @apiEntity schema/user/processEntity.json
 *
 * @property int    $id
 * @property string $name
 * @property string $event
 * @property int    $user_id
 * @property int    $created_at
 * @property int    $updated_at
 */
class Process extends AbstractEntity {
    /**
     * {@inheritdoc}
     */
    protected $visible = ['id', 'name', 'event', 'created_at', 'updated_at'];
    /**
     * {@inheritdoc}
     */
    protected $dates = ['created_at', 'updated_at'];
}
