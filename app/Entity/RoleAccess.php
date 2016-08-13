<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Entity;

/**
 * RoleAccesss Entity.
 *
 * @apiEntity schema/setting/settingEntity.json
 *
 * @property int 	    $id
 * @property int        $identity_id
 * @property string     $role
 * @property string     $resource
 * @property int 	    $access
 */
class RoleAccess extends AbstractEntity {
    /**
     * Cache prefix.
     */
    const CACHE_PREFIX      = 'RoleAccess';

    /**
     * Access levels following UNIX file permission standard.
     */
    const ACCESS_NONE       = 0x00;
    const ACCESS_EXECUTE    = 0x01;
    const ACCESS_WRITE      = 0x02;
    const ACCESS_READ       = 0x04;

    /**
     * {@inheritdoc}
     */
    protected $visible = ['id', 'role', 'access', 'resource', 'created_at', 'updated_at'];

    /**
     * {@inheritdoc}
     */
    protected $dates = ['created_at', 'updated_at'];

    /**
     * {@inheritdoc}
     */
    public function getCacheKeys() : array {
        return [
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getReferenceCacheKeys() : array {
        return array_merge([
        ],
        $this->getCacheKeys());
    }
}
