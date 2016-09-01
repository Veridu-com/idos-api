<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Entity;

use App\Extension\SecureFields;

/**
 * Process Entity.
 *
 * @apiEntity schema/user/processEntity.json
 *
 * @property int    $id
 * @property string $name
 * @property string $event
 * @property int    $user_id
 * @property int    $created_at
 * @property int    $updated_at
 */
class Process extends AbstractEntity {
    use SecureFields;

    /**
     * {@inheritdoc}
     */
    const CACHE_PREFIX = 'Process';

    /**
     * {@inheritdoc}
     */
    protected $visible = ['id', 'name', 'event', 'created_at', 'updated_at'];
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
            )
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
