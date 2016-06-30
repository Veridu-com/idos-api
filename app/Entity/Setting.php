<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Entity;

/**
 * Settings Entity.
 *
 * @apiEntity schema/setting/settingEntity.json
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
