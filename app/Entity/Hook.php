<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Entity;

use App\Extension\SecureFields;

/**
 * Hooks Entity.
 *
 * @apiEntity schema/hook/hookEntity.json
 */
class Hook extends AbstractEntity {
    use SecureFields;
    /**
     * {@inheritdoc}
     */
    const CACHE_PREFIX = 'Hook';

    /**
     * {@inheritdoc}
     */
    protected $visible = ['id', 'trigger', 'url', 'subscribed', 'created_at', 'updated_at'];
    /**
     * {@inheritdoc}
     */
    protected $dates = ['created_at', 'updated_at'];
    /**
     * {@inheritdoc}
     */
    protected $secure = ['url'];

    /**
     * {@inheritdoc}
     */
    public function getCacheKeys() : array {
        return [
            sprintf(
                '%s/id/%s',
                self::CACHE_PREFIX,
                $this->id
            ),
            sprintf(
                '%s/slug/%s',
                self::CACHE_PREFIX,
                $this->slug
            ),
            sprintf(
                '%s/public/%s',
                self::CACHE_PREFIX,
                $this->public
            )
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getReferenceCacheKeys() : array {
        return array_merge(
            [],
            $this->getCacheKeys()
        );
    }
}
