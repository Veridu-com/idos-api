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
 * Tags Entity.
 *
 * @apiEntity schema/tag/tagEntity.json
 *
 * @property int $id
 * @property int $user_id
 */
class Tag extends AbstractEntity {
    use SlugMutator;

    /**
     * {@inheritdoc}
     */
    protected $visible = ['id', 'name', 'slug', 'created_at', 'updated_at'];
    /**
     * {@inheritdoc}
     */
    protected $dates = ['created_at', 'updated_at'];
}
