<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Entity;

use App\Helper\Utils;

/**
 * Tags Entity.
 *
 * @apiEntity schema/tag/tagEntity.json
 *
 * @property int $id
 * @property int $user_id
 */
class Tag extends AbstractEntity {
    /**
     * {@inheritdoc}
     */
    protected $visible = ['id', 'name', 'slug', 'created_at', 'updated_at'];
    /**
     * {@inheritdoc}
     */
    protected $dates = ['created_at', 'updated_at'];

    /**
     * Property Mutator for $name.
     *
     * @param string $value
     *
     * @return App\Entity\Tag
     */
    public function setNameAttribute(string $value) : self {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Utils::slugify($value);

        return $this;
    }
}
