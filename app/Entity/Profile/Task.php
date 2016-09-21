<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Entity\Profile;

use App\Entity\AbstractEntity;

/**
 * Task Entity.
 *
 * @apiEntity schema/tasks/taskEntity.json
 *
 * @property int     $id
 * @property string  $name
 * @property string  $event
 * @property string  $message
 * @property bool $running
 * @property bool $success
 * @property int     $process_id
 * @property int     $created_at
 * @property int     $updated_at
 */
class Task extends AbstractEntity {
    /**
     * {@inheritdoc}
     */
    protected $visible = [
        'id',
        'name',
        'event',
        'running',
        'success',
        'created_at',
        'updated_at'
    ];
    /**
     * {@inheritdoc}
     */
    protected $dates = ['created_at', 'updated_at'];
    /**
     * {@inheritdoc}
     */
    protected $secure = ['message'];
}
