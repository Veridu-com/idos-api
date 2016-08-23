<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Entity;

/**
 * Tags Entity.
 *
 * @apiEntity schema/tag/tagEntity.json
 *
 * @property int $id
 * @property int $user_id
 */
class Tag extends AbstractEntity {
    /**
     * {@inheritdoc}
     */
    const CACHE_PREFIX = 'Tag';
    /**
     * {@inheritdoc}
     */
    protected $visible = ['id', 'user_id', 'name', 'created_at', 'updated_at'];
    /**
     * {@inheritdoc}
     */
    protected $dates = ['created_at', 'updated_at'];

    /**
     * {@inheritdoc}
     */
    //public $relationships = ['user' => 'User'];

    /**
     * {@inheritdoc}
     */
    public function getCacheKeys() : array {
        return [
            sprintf('%s.id.%s', self::CACHE_PREFIX, $this->id),
            sprintf('%s.user_id.%s', self::CACHE_PREFIX, $this->userId)
        ];
    }

    public function getReferenceCacheKeys() : array {
        return array_merge([
            sprintf('%s.by.company_id.%s', self::CACHE_PREFIX, $this->companyId)
        ],
        $this->getCacheKeys());
    }

}
