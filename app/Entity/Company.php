<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Entity;

use App\Helper\Utils;

/**
 * Companies Entity.
 *
 * @apiEntity Company
 * @apiEntityRequiredProperty string name Company name
 * @apiEntityProperty string slug Slug based on company's name
 * @apiEntityProperty string public_key Public Key for management calls
 * @apiEntityProperty int created Company creation Unixtimestamp
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $public_key
 * @property string $private_key
 * @property string $created_at
 * @property string $updated_at
 * @property int $created
 */
class Company extends AbstractEntity {
    /**
     * {@inheritdoc}
     */
    protected $visible = ['name', 'slug', 'public_key', 'created_at'];

    public function setNameAttribute($value) {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Utils::slugify($value);
    }
}
