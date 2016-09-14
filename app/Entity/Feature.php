<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Entity;

use App\Extension\SlugMutator;

/**
 * Features Entity.
 *
 * @apiEntity schema/user/featureEntity.json
 *
 * @property int    $id
 * @property string $name
 * @property string $slug
 * @property string $value
 * @property int    $user_id
 * @property int    $created_at
 * @property int    $updated_at
 */
class Feature extends AbstractEntity {
    use SlugMutator;

    /**
     * {@inheritdoc}
     */
    protected $visible = [
        'name',
        'slug',
        'value',
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
    protected $secure = ['value'];
}
