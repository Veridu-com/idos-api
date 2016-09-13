<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Entity;

use App\Extension\SecureFields;

/**
 * Hooks Entity.
 *
 * @apiEntity schema/hook/hookEntity.json
 */
class Hook extends AbstractEntity {
    use SecureFields;

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
