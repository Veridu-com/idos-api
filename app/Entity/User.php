<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Entity;

/**
 * Users Entity.
 *
 * @apiEntity schema/user/userEntity.json
 */
class User extends AbstractEntity {
    /**
     * {@inheritdoc}
     */
    protected $visible = ['id', 'username', 'credential', 'recommendation', 'created_at', 'updated_at'];
    /**
     * {@inheritdoc}
     */
    protected $dates = ['created_at', 'updated_at'];
    /**
     * {@inheritdoc}
     */
    public $relationships = [
        'credential' => 'Company\Credential'
    ];
}
