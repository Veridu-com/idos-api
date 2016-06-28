<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Entity;

/**
 * Settings Entity.
 *
 * @apiEntity Setting
 * @apiEntityRequiredProperty 	int 	company_id 	Company owner of the Setting
 * @apiEntityRequiredProperty 	string 	section 	setting's section name
 * @apiEntityRequiredProperty 	string 	property 	setting property name
 * @apiEntityRequiredProperty 	string 	value  		setting value
 *
 * @property int 	$id
 * @property int 	$company_id
 * @property string $section
 * @property string $property
 * @property string $value
 */
class Setting extends AbstractEntity {
    /**
     * {@inheritdoc}
     */
    protected $visible = ['section', 'property', 'value', 'created_at'];

    public function getValueAttribute($value) {
        return  is_string($value) ? $value : stream_get_contents($value, -1, 0);
    }
}
