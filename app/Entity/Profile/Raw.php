<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Entity\Profile;

use App\Entity\AbstractEntity;

/**
 * Raw Entity.
 *
 * @apiEntity schema/raw/rawEntity.json
 *
 * @property int    $id
 * @property int    $source_id
 * @property string $collection
 * @property string $data
 * @property int    $created_at
 */
class Raw extends AbstractEntity {
    /**
     * {@inheritdoc}
     */
    protected $visible = ['source_id', 'collection', 'data', 'created_at', 'updated_at'];

    /**
     * {@inheritdoc}
     */
    protected $dates = ['created_at', 'updated_at'];

    /**
     * {@inheritdoc}
     */
    protected $json = ['data'];

    /**
     * {@inheritdoc}
     */
    protected $compressed = ['data'];

    /**
     * {@inheritdoc}
     */
    protected $secure = ['data'];
}
