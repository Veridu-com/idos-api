<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Entity;

use App\Extension\SlugMutator;

/**
 * Warnings Entity.
 *
 * @apiEntity schema/warning/warningEntity.json
 *
 * @property int    $id
 * @property string $name
 * @property string $slug
 * @property int    $user_id
 * @property int    $created_at
 * @property int    $updated_at
 */
class Warning extends AbstractEntity {
    use SlugMutator;

    /**
     * {@inheritdoc}
     */
    protected $visible = [
        'id',
        'creator',
        'name',
        'slug',
        'reference',
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
    public $relationships = [
        'creator' => 'Service'
    ];
}
