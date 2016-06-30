<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Entity;

use App\Helper\Utils;

/**
 * Credentials Entity.
 *
 * @apiEntity schema/credential/credentialEntity.json
 *
 * @property int $id
 * @property string $company_id
 * @property string $name
 * @property string $slug
 * @property string $public
 * @property string $private
 * @property string $production
 * @property string $created_at
 * @property string $updated_at
 */
class Credential extends AbstractEntity {
    /**
     * {@inheritdoc}
     */
    protected $visible = ['name', 'slug', 'public', 'created_at'];

    public function setNameAttribute($value) {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Utils::slugify($value);
    }
}
