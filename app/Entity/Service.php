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
 * @property int        $handler_service_id
 * @property array      $listens
 * @property int        $created_at
 * @property int        $updated_at
 */
class Service extends AbstractEntity {
    /**
     * {@inheritdoc}
     */
    protected $visible = ['id', 'listens', 'handler_service', 'created_at', 'updated_at'];
    /**
     * {@inheritdoc}
     */
    protected $json = ['listens'];
    /**
     * {@inheritdoc}
     */
    public $relationships = [
        'handler_service' => 'HandlerService'
    ];
    /**
     * {@inheritdoc}
     */
    protected $dates = ['created_at', 'updated_at'];
}
