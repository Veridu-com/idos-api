<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Entity\Company;

use App\Entity\AbstractEntity;

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
    protected $visible = ['id', 'company_id', 'identity_id', 'role', 'created_at', 'updated_at'];
    /**
     * {@inheritdoc}
     */
    protected $dates = ['created_at', 'updated_at'];
    /**
     * {@inheritdoc}
     */
    protected $obfuscated = ['id', 'company_id', 'identity_id'];

    /**
     * {@inheritdoc}
     */
    public $relationships = [
        'identity' => 'Identity',
        'role'     => 'Role',
        'company'  => 'Company'
    ];
}
