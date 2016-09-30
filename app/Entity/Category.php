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
 * @property string     $name
 * @property string     $url
 * @property array      $listens
 * @property array      $triggers
 * @property bool       $enabled
 * @property int        $access
 * @property int        $created_at
 * @property int        $updated_at
 */
class Category extends AbstractEntity {
    /**
     * {@inheritdoc}
     */
    protected $visible = [
        'name',
        'slug',
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
