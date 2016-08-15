<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Entity;

/**
 * Service's Entity.
 *
 * @apiEntity schema/service/serviceEntity.json
 *
 * @property int        $id
 * @property string     $name
 * @property string     $url
 * @property array      $listens
 * @property array      $triggers
 * @property bool       $enabled
 * @property int        $created_at
 * @property int        $updated_at
 */
class Service extends AbstractEntity {
    /**
     * Cache prefix.
     */
    const CACHE_PREFIX = 'Service';

    /**
     * {@inheritdoc}
     */
    protected $visible = [ 'id', 'name', 'url', 'enabled', 'listens', 'triggers', 'created_at', 'updated_at'];

    /**
     * {@inheritdoc}
     */
    protected $dates = ['created_at', 'updated_at'];

    /**
     * {@inheritdoc}
     */
    protected $json = ['listens', 'triggers'];

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
