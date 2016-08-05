<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Entity;

use App\Extension\NameToSlugMutator;

/**
 * Service's Entity.
 *
 * @apiEntity schema/service/serviceEntity.json
 *
 * @property int        $id
 * @property string     $name
 * @property string     $slug
 * @property bool    $enabled
 * @property int        $created_at
 * @property int        $updated_at
 */
class Service extends AbstractEntity {
    use NameToSlugMutator;

    /**
     * Cache prefix.
     */
    const CACHE_PREFIX = 'Service';

    /**
     * {@inheritdoc}
     */
    protected $visible = ['name', 'slug', 'enabled', 'created_at'];

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
