<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Entity;

/**
 * Sources Entity.
 *
 * @apiEntity schema/source/credentialEntity.json
 */
class Source extends AbstractEntity {
    /**
     * {@inheritdoc}
     */
    const CACHE_PREFIX = 'Source';

    /**
     * {@inheritdoc}
     */
    protected $visible = ['id', 'name', 'tags'];
    /**
     * {@inheritdoc}
     */
    protected $dates = ['created_at', 'updated_at'];

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
        return array_merge(
            [
            sprintf(
                '%s.by.parent_id.%s',
                self::CACHE_PREFIX,
                $this->parentId
            )
            ],
            $this->getCacheKeys()
        );
    }
}
