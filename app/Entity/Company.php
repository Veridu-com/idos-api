<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Entity;

use App\Helper\Utils;

/**
 * Companies Entity.
 *
 * @apiEntity schema/company/companyEntity.json
 *
 * @property int    $id
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
    const CACHE_PREFIX = 'Company';

    /**
     * {@inheritdoc}
     */
    protected $visible = ['name', 'slug', 'public_key', 'created_at', 'updated_at'];
    /**
     * {@inheritdoc}
     */
    protected $dates = ['created_at', 'updated_at'];

    /**
     * Property Mutator (setter) for $name.
     *
     * @param string $value
     *
     * @return App\Entity\Company
     */
    public function setNameAttribute(string $value) : self {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Utils::slugify($value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheKeys() : array {
        return [
            sprintf(
                '%s.id.%s',
                self::CACHE_PREFIX,
                $this->id
            ),
            sprintf(
                '%s.slug.%s',
                self::CACHE_PREFIX,
                $this->slug
            ),
            sprintf(
                '%s.private_key.%s',
                self::CACHE_PREFIX,
                $this->private_key
            ),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getReferenceCacheKeys() : array {
        return array_merge([
            sprintf(
                '%s.by.parent_id.%s',
                self::CACHE_PREFIX,
                $this->parentId
            )
        ],
        $this->getCacheKeys());
    }
}
