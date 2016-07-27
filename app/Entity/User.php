<?php

/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Entity;

use App\Helper\Utils;

/**
 * Users Entity.
 *
 * @apiEntity schema/credential/credentialEntity.json
 *
 * @property int $id
 * @FIXME
 */
class User extends AbstractEntity {
    /**
     * {@inheritdoc}
     */
    const CACHE_PREFIX = 'User';
    /**
     * {@inheritdoc}
     */
    protected $visible = ['name', 'slug', 'public', 'created_at'];
    /**
     * {@inheritdoc}
     */
    protected $dates = ['created_at', 'updated_at'];

    public function setNameAttribute($value) {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Utils::slugify($value);
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheKeys() : array {
        return [
            sprintf('%s/id/%s', self::CACHE_PREFIX, $this->id),
            sprintf('%s/slug/%s', self::CACHE_PREFIX, $this->slug),
            sprintf('%s/public/%s', self::CACHE_PREFIX, $this->public)
        ];
    }
}
