<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Entity;

use App\Helper\Utils;

/**
 * Companies Entity.
 *
 * @apiEntity schema/company/companyEntity.json
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $public_key
 * @property string $private_key
 * @property int    $created_at
 * @property int    $updated_at
 */
class Company extends AbstractEntity {
    /**
     * {@inheritdoc}
     */
    protected $visible = ['name', 'slug', 'public_key', 'created_at'];
    /**
     * {@inheritdoc}
     */
    protected $dates = ['created_at', 'updated_at'];

    public function setNameAttribute($value) {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Utils::slugify($value);
    }
}
