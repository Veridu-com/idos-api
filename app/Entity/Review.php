<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Entity;

/**
 * Review Entity.
 *
 * @apiEntity schema/review/reviewEntity.json
 *
 * @property int    $id
 * @property bool $positive
 * @property int    $created_at
 * @property int    $updated_at
 */
class Review extends AbstractEntity {
    /**
     * {@inheritdoc}
     */
    const CACHE_PREFIX = 'Review';

    /**
     * {@inheritdoc}
     */
    protected $visible = ['id', 'positive', 'created_at'];
    /**
     * {@inheritdoc}
     */
    protected $dates = ['created_at'];

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
                '%s.user_id.%s',
                self::CACHE_PREFIX,
                $this->user_id
            ),
            sprintf(
                '%s.warning_id.%s',
                self::CACHE_PREFIX,
                $this->warning_id
            ),
            sprintf(
                '%s.positive.%s',
                self::CACHE_PREFIX,
                $this->positive
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
            ),
            sprintf(
                '%s.by.warning_id.%s',
                self::CACHE_PREFIX,
                $this->warningId
            )
            ],
            $this->getCacheKeys()
        );
    }
}
