<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Entity\Profile;

use App\Entity\AbstractEntity;
use App\Extension\SlugMutator;

/**
 * Gates Entity.
 *
 * @apiEntity schema/gate/gateEntity.json
 *
 * @property int    $id
 * @property string $name
 * @property string $slug
 * @property bool $pass
 * @property int    $user_id
 * @property int    $created_at
 * @property int    $updated_at
 */
class Gate extends AbstractEntity {
    use SlugMutator;

    /**
     * {@inheritdoc}
     */
    protected $visible = [
        'creator',
        'name',
        'slug',
        'pass',
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
