<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Entity\Company;

use App\Entity\AbstractEntity;

/**
 * Invitations Entity.
 *
 * @apiEntity schema/user/userEntity.json
 */
class Invitation extends AbstractEntity {
    /**
     * {@inheritdoc}
     */
    protected $visible = ['id', 'email', 'role', 'expires', 'voided', 'created_at', 'updated_at'];
    /**
     * {@inheritdoc}
     */
    protected $dates = ['created_at', 'updated_at', 'expires'];
    /**
     * {@inheritdoc}
     */
    public $relationships = [
        'credential' => 'Company\Credential'
    ];
}
