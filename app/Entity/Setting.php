<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Entity;

/**
 * Settings Entity.
 *
 * @apiEntity schema/setting/settingEntity.json
 *
 * @property int    $id
 * @property int    $company_id
 * @property string $section
 * @property string $property
 * @property string $value
 * @property int    $created_at
 * @property int    $updated_at
 */
class Setting extends AbstractEntity {
    /**
     * {@inheritdoc}
     */
    protected $visible = [
        'id',
        'section',
        'property',
        'value',
        'created_at',
        'updated_at'
    ];
    /**
     * {@inheritdoc}
     */
    protected $dates = ['created_at', 'updated_at'];
    /**
     * {@inheritdoc}
     */
    protected $secure = ['value'];
}
