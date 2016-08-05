<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Entity;

use App\Extension\SecureFields;
use App\Helper\Utils;

/**
 * Credentials Entity.
 *
 * @apiEntity schema/credential/credentialEntity.json
 *
 * @property int    $id
 * @property string $company_id
 * @property string $name
 * @property string $slug
 * @property string $public
 * @property string $private
 * @property string $production
 * @property int    $created_at
 * @property int    $updated_at
 */
class Credential extends AbstractEntity {
    use SecureFields;
    /**
     * {@inheritdoc}
     */
    const CACHE_PREFIX = 'Credential';

    /**
     * {@inheritdoc}
     */
    protected $visible = ['name', 'slug', 'public', 'created_at', 'updated_at'];
    /**
     * {@inheritdoc}
     */
    protected $dates = ['created_at', 'updated_at'];
    /**
     * The attributes that should be secured.
     *
     * @var array
     */
    protected $secure = ['private'];

    /**
     * Property mutator (setter) for $name.
     *
     * @param string $value
     *
     * @return App\Entity\Credential
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
                '%s.public.%s',
                self::CACHE_PREFIX,
                $this->public
            )
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getReferenceCacheKeys() : array {
        return array_merge([
            sprintf(
                '%s.by.company_id.%s',
                self::CACHE_PREFIX,
                $this->companyId
            )
        ],
        $this->getCacheKeys());
    }
}
