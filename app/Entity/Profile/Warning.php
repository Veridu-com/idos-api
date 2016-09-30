<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Entity\Profile;

use App\Entity\AbstractEntity;

/**
 * Warnings Entity.
 *
 * @apiEntity schema/warning/warningEntity.json
 *
 * @property int    $id
 * @property string $slug
 * @property int    $user_id
 * @property int    $created_at
 * @property int    $updated_at
 */
class Warning extends AbstractEntity {
    /**
     * {@inheritdoc}
     */
    protected $visible = [
        'id',
        'creator',
        'warning',
        'slug',
        'attribute',
        'created_at',
        'updated_at'
    ];
    /**
     * {@inheritdoc}
     */
    protected $dates = ['id', 'created_at', 'updated_at'];

    /**
     * {@inheritdoc}
     */
    public $relationships = [
        'creator' => 'Service',
        'warning' => 'Warning'
    ];
}
