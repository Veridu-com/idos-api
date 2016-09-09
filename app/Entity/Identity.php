<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Entity;

use App\Extension\SecureFields;

/**
 * Identities Entity.
 *
 * @apiEntity schema/identity/identityEntity.json
 */
class Identity extends AbstractEntity {
    use SecureFields;

    /**
     * {@inheritdoc}
     */
    protected $visible = [
        'id',
        'reference',
        'public_key',
        'private_key',
        'created_at',
        'updated_at'
    ];
    /**
     * {@inheritdoc}
     */
    protected $dates = ['created_at', 'updated_at'];

    /**
     * The attributes that should be secured.
     *
     * @var array
     */
    protected $secure = ['private_key'];
}