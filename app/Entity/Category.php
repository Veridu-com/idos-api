<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Entity;

/**
 * Category's Entity.
 *
 * @apiEntity schema/service/serviceEntity.json
 *
 * @property int        $id
 * @property string     $displayName
 * @property string     $name
 * @property string     $type
 * @property string     $description
 * @property int        $created_at
 * @property int        $updated_at
 */
class Category extends AbstractEntity {
    /**
     * {@inheritdoc}
     */
    protected $visible = [
        'id',
        'display_name',
        'name',
        'type',
        'description',
        'created_at',
        'updated_at'
    ];
    /**
     * {@inheritdoc}
     */
    protected $dates = ['created_at', 'updated_at'];
}
