<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Entity;

/**
 * Service's Entity.
 *
 * @apiEntity schema/service/serviceEntity.json
 *
 * @property int        $id
 * @property int        $company_id
 * @property string     $name
 * @property string     $privacy
 * @property bool       $enabled
 * @property int        $created_at
 * @property int        $updated_at
 */
class HandlerService extends AbstractEntity {
    /**
     * Privacy constants.
     *
     * @see \App\Handler\Company@setup
     */
    const PRIVACY_PUBLIC  = 0x00;
    const PRIVACY_PRIVATE = 0x01;

    /**
     * {@inheritdoc}
     */
    protected $visible = [
        'id',
        'name',
        'url',
        'listens',
        'enabled',
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
    protected $json = ['listens'];
}
