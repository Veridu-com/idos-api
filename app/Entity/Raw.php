<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Entity;

use App\Extension\SecureFields;

/**
 * Raw Entity.
 *
 * @apiEntity schema/raw/rawEntity.json
 *
 * @property int    $id
 * @property string $name
 * @property string $data
 * @property int    $created_at
 */
class Raw extends AbstractEntity {
    use SecureFields;
    /**
     * {@inheritdoc}
     */
    const CACHE_PREFIX = 'Raw';

    /**
     * {@inheritdoc}
     */
    protected $visible = ['name', 'data', 'created_at'];
    /**
     * {@inheritdoc}
     */
    protected $dates = ['created_at', 'updated_at'];
    /**
     * The attributes that should be secured.
     *
     * @var array
     */
    protected $secure = ['data'];

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
                '%s.name.%s',
                self::CACHE_PREFIX,
                $this->name
            ),
            sprintf(
                '%s.data.%s',
                self::CACHE_PREFIX,
                $this->data
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
                '%s.by.source_id.%s',
                self::CACHE_PREFIX,
                $this->sourceId
            )
            ],
            $this->getCacheKeys()
        );
    }
}
