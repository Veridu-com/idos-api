<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Entity\Company;

use App\Entity\AbstractEntity;

/**
 * Hooks Entity.
 *
 * @apiEntity schema/hook/hookEntity.json
 */
class Hook extends AbstractEntity {
    /**
     * {@inheritdoc}
     */
    protected $visible = [
        'id',
        'trigger',
        'url',
        'subscribed',
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
    protected $secure = ['url'];
}
