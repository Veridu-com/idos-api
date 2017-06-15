<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Entity\Company;

use App\Entity\AbstractEntity;

/**
 * Widgets Entity.
 *
 * @apiEntity schema/widget/widgetEntity.json
 */
class Widget extends AbstractEntity {
    /**
     * {@inheritdoc}
     */
    protected $visible = [
        'hash',
        'label',
        'type',
        'config',
        'credential',
        'created_at',
        'updated_at'
    ];
    /**
     * {@inheritdoc}
     */
    protected $json = [
        'config'
    ];
    /**
     * {@inheritdoc}
     */
    protected $dates = ['created_at', 'updated_at'];
    /**
     * {@inheritdoc}
     */
    public $relationships = [
        'credential' => 'Company\Credential'
    ];
}
