<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Entity;

/**
 * ServiceHandler's Entity.
 *
 * @apiEntity schema/service-handler/serviceHandlerEntity.json
 *
 * @property int        $id
 * @property int        $company_id
 * @property int        $service_id
 * @property array      $listens
 * @property int        $created_at
 * @property int        $updated_at
 */
class ServiceHandler extends AbstractEntity {
    /**
     * {@inheritdoc}
     */
    protected $visible = ['id', 'listens', 'service', 'created_at', 'updated_at'];
    /**
     * {@inheritdoc}
     */
    protected $json = ['listens'];
    /**
     * {@inheritdoc}
     */
    public $relationships = [
        'service' => 'Service'
    ];
    /**
     * {@inheritdoc}
     */
    protected $dates = ['created_at', 'updated_at'];
    /**
     * {@inheritdoc}
     */
    protected $secure = ['username', 'password'];
}
