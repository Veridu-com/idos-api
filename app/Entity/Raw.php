<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Entity;

use App\Extension\SecureFields;

/**
 * Raw Entity.
 *
 * @apiEntity schema/raw/rawEntity.json
 *
 * @property int    $id
 * @property string $collection
 * @property string $data
 * @property int    $created_at
 */
class Raw extends AbstractEntity {
    use SecureFields;

    /**
     * {@inheritdoc}
     */
    protected $visible = ['collection', 'data', 'created_at'];
    /**
     * {@inheritdoc}
     */
    protected $dates = ['created_at', 'updated_at'];
    /**
     * The attributes that should be secured.
     *
     * @var array
     */
    protected $secure = ['data'];
}
