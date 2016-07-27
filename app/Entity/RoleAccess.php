<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Entity;

/**
 * RoleAccesss Entity.
 *
 * @apiEntity schema/setting/settingEntity.json
 *
 * @property int 	$id
 * @property int 	$company_id
 * @property string $section
 * @property string $property
 * @property string $value
 * @property int    $created_at
 * @property int    $updated_at
 * @FIXME fix comments
 */
class RoleAccess extends AbstractEntity {
    /**
     * Cache prefix.
     */
    const CACHE_PREFIX      = 'RoleAccess';
    
    /**
     * Access levels following UNIX file permission standard
     */
    const ACCESS_READ       = '4';
    const ACCESS_WRITE      = '2';
    const ACCESS_READWRITE  = '6';
    const ACCESS_FORBIDDEN  = '0';

    /**
     * {@inheritdoc}
     */
    protected $visible = ['role', 'access', 'resource', 'created_at', 'updated_at'];

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
