<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Entity;

/**
 * Identities Entity.
 *
 * @apiEntity schema/identity/identityEntity.json
 */
class Identity extends AbstractEntity {
    /**
     * {@inheritdoc}
     */
    protected $visible = [
        'id',
        'reference',
        'public_key',
        'created_at',
        'updated_at',
        'member',
        'company',
    ];
    /**
     * {@inheritdoc}
     */
    protected $dates = ['created_at', 'updated_at'];
    /**
     * {@inheritdoc}
     */
    protected $secure = ['private_key'];

    /**
     * {@inheritdoc}
     */
    public $relationships = [
        'role'    => 'Role',
        'company' => 'Company',
        'member'  => 'Member'
    ];
}
