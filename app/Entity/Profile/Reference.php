<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Entity\Profile;

use App\Entity\AbstractEntity;

/**
 * Reference Entity.
 *
 * @apiEntity schema/reference/referenceEntity.json
 *
 * @property int    $id
 * @property string $name
 * @property string $value
 * @property int    $created_at
 * @property int    $updated_at
 */
class Reference extends AbstractEntity {
    /**
     * {@inheritdoc}
     */
    protected $visible = ['name', 'value', 'created_at'];
    /**
     * {@inheritdoc}
     */
    protected $dates = ['created_at'];
    /**
     * {@inheritdoc}
     */
    protected $secure = ['value'];
}
