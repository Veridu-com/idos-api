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
    protected $visible = ['id', 'user', 'role', 'created_at', 'updated_at'];
    /**
     * {@inheritdoc}
     */
    protected $dates = ['created_at', 'updated_at'];

    /**
     * {@inheritdoc}
     */
    public $relationships = [
        'user' => 'User'
    ];
}
