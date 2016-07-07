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
    protected $visible = ['route_name', 'created_at'];


}
