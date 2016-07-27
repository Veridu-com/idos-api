<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Entity;

use App\Helper\Utils;

/**
 * Users Entity.
 *
 * @apiEntity schema/credential/credentialEntity.json
 *
 * @property int $id
 * @FIXME list all user props
 */
class User extends AbstractEntity {
    /**
     * {@inheritdoc}
     */
    const CACHE_PREFIX = 'User';

    /**
     * {@inheritdoc}
     */
    protected $visible = ['username', 'role', 'created_at'];

    /**
     * {@inheritdoc}
     */
    protected $dates = ['created_at', 'updated_at'];

    public function setUsernameAttribute($value) {
        $this->attributes['username'] = is_string($value) ? $value : stream_get_contents($value, -1, 0);
        return $this;
    }

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
