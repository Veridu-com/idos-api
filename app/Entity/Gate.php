<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Entity;

use App\Extension\SecureFields;
use App\Helper\Utils;

/**
 * Gates Entity.
 *
 * @apiEntity schema/gate/gateEntity.json
 *
 * @property int    $id
 * @property string $name
 * @property string $slug
 * @property bool $pass
 * @property int    $user_id
 * @property int    $created_at
 * @property int    $updated_at
 */
class Gate extends AbstractEntity {
    use SecureFields;
    /**
     * {@inheritdoc}
     */
    const CACHE_PREFIX = 'Gate';

    /**
     * {@inheritdoc}
     */
    protected $visible = ['name', 'slug', 'pass', 'user_id', 'created_at', 'updated_at'];
    /**
     * {@inheritdoc}
     */
    protected $dates = ['created_at', 'updated_at'];

    /**
     * Property Mutator for $name.
     *
     * @param string $name
     *
     * @return App\Entity\Gate
     */
    public function setNameAttribute(string $name) : self {
        $this->attributes['name'] = $name;
        $this->attributes['slug'] = Utils::slugify($name);

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
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getReferenceCacheKeys() : array {
        return array_merge(
            [
                sprintf(
                    '%s.by.user_id.%s',
                    self::CACHE_PREFIX,
                    $this->userId
                )
            ],
            $this->getCacheKeys()
        );
    }
}
