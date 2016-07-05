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
 * @apiEntityRequiredProperty 	string 	route_name 	permission's route's name associated
 *
 * @property int 	$id
 * @property int 	$company_id
 * @property string $route_name
 */
class Permission extends AbstractEntity {
    /**
     * {@inheritdoc}
     */
    protected $visible = ['route_name'];

    public function getValueAttribute($value) {
        return  is_string($value) ? $value : stream_get_contents($value, -1, 0);
    }
}
