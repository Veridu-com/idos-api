<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Entity;

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
    /**
     * {@inheritdoc}
     */
    protected $visible = ['collection', 'data', 'created_at'];

    /**
     * {@inheritdoc}
     */
    protected $dates = ['created_at', 'updated_at'];

    /**
     * {@inheritdoc}
     */
    protected $secure = ['data'];
}
