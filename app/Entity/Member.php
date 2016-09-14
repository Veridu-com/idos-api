<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Entity;

/**
 * Members Entity.
 *
 * @apiEntity schema/member/memberEntity.json
 *
 * @property int $id
 * @property int $user_id
 */
class Member extends AbstractEntity {
    /**
     * {@inheritdoc}
     */
    protected $visible = ['id', 'company_id', 'identity', 'role', 'created_at', 'updated_at'];
    /**
     * {@inheritdoc}
     */
    protected $dates = ['created_at', 'updated_at'];

    /**
     * {@inheritdoc}
     */
    protected $obfuscated = ['id', 'company_id'];

    /**
     * {@inheritdoc}
     */
    public $relationships = [
        'identity' => 'Identity',
        'company'  => 'Company',
        'role'     => 'Role'
    ];
}
