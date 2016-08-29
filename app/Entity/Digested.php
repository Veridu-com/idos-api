<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Entity;

use App\Extension\SecureFields;

/**
 * Digested Entity.
 *
 * @apiEntity schema/digested/digestedEntity.json
 *
 * @property int    $id
 * @property string $name
 * @property string $value
 * @property int    $created_at
 * @property int    $updated_at
 */
class Digested extends AbstractEntity {
    use SecureFields;
    /**
     * {@inheritdoc}
     */
    const CACHE_PREFIX = 'Digested';

    /**
     * {@inheritdoc}
     */
    protected $visible = ['name', 'value', 'created_at'];
    /**
     * {@inheritdoc}
     */
    protected $dates = ['created_at'];
    /**
     * The attributes that should be secured.
     *
     * @var array
     */
    protected $secure = ['value'];

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
                '%s.source_id.%s',
                self::CACHE_PREFIX,
                $this->source_id
            ),
            sprintf(
                '%s.name.%s',
                self::CACHE_PREFIX,
                $this->name
            ),
            sprintf(
                '%s.value.%s',
                self::CACHE_PREFIX,
                $this->value
            ),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getReferenceCacheKeys() : array {
        return array_merge([
            sprintf(
                '%s.by.source_id.%s',
                self::CACHE_PREFIX,
                $this->sourceId
            )
        ],
        $this->getCacheKeys());
    }
}
