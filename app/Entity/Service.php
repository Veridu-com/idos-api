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
 * @property string     $name
 * @property string     $url
 * @property array      $listens
 * @property array      $triggers
 * @property bool       $enabled
 * @property int        $access
 * @property int        $created_at
 * @property int        $updated_at
 */
class Service extends AbstractEntity {
    /**
     * Only the owning company have access.
     *
     * @const ACCESS_PRIVATE
     */
    const ACCESS_PRIVATE = 0x00;
    /**
     * Children companies have "read" access.
     *
     * @const ACCESS_PROTECTED
     */
    const ACCESS_PROTECTED = 0x01;
    /**
     * Any company have "read" access.
     *
     * @const ACCESS_PUBLIC
     */
    const ACCESS_PUBLIC = 0x02;

    /**
     * {@inheritdoc}
     */
    protected $visible = [
        'id',
        'name',
        'url',
        'public',
        'access',
        'listens',
        'triggers',
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
    protected $json = ['listens', 'triggers'];
    /**
     * {@inheritdoc}
     */
    protected $secure = ['auth_username', 'auth_password', 'private'];
}
