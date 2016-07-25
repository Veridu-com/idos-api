<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Entity;

/**
 * Permissions Entity.
 *
 * @apiEntity Permission
 * @apiEntityRequiredProperty 	int 	company_id 	Company owner of the Permission
 * @apiEntityRequiredProperty 	string 	routeName 	permission's route's name associated
 *
 * @property int 	$id
 * @property int 	$company_id
 * @property string $routeName
 */
class Permission extends AbstractEntity {
    /**
     * {@inheritdoc}
     */
    const CACHE_PREFIX = 'Permission';
    /**
     * {@inheritdoc}
     */
    protected $visible = ['route_name', 'created_at'];

    /**
     * {@inheritdoc}
     */
    protected $dates = ['created_at'];

    /**
     * {@inheritdoc}
     */
    public function getCacheKeys() : array {
        return [
            sprintf('%s.id.%s', self::CACHE_PREFIX, $this->id),
            sprintf('%s.public.%s', self::CACHE_PREFIX, $this->public)
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function getReferenceCacheKeys() : array {
        return array_merge([
            sprintf('%s.by.parent_id.%s', self::CACHE_PREFIX, $this->parentId)
        ],
        $this->getCacheKeys());
    }
}
