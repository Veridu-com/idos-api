<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Entity\Company;

use App\Entity\AbstractEntity;

/**
 * Permissions Entity.
 *
 * @apiEntity schema/permission/permissionEntity.json
 *
 * @property int    $id
 * @property int    $company_id
 * @property string $route_name
 */
class Permission extends AbstractEntity {
    /**
     * {@inheritdoc}
     */
    protected $visible = ['route_name', 'created_at'];
    /**
     * {@inheritdoc}
     */
    protected $dates = ['created_at'];
}
