<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Entity;

/**
 * CompanyDaemonHandler's Entity.
 *
 * @apiEntity schema/daemon-handler/daemonHandlerEntity.json
 *
 * @property int        $id
 * @property int        $company_id
 * @property int        $daemon_handler_id
 * @property int        $created_at
 * @property int        $updated_at
 */
class CompanyDaemonHandler extends AbstractEntity {
    /**
     * Cache prefix.
     */
    const CACHE_PREFIX = 'CompanyDaemonHandler';

    /**
     * {@inheritdoc}
     */
    protected $visible = ['id', 'daemon_handler', 'created_at', 'updated_at'];

    /**
     * {@inheritdoc}
     */
    protected $dates = ['created_at', 'updated_at'];

    /**
     * {@inheritdoc}
     */
    public $relationships = [
        'daemon_handler' => 'DaemonHandler'
    ];

    /**
     * {@inheritdoc}
     */
    public function getCacheKeys() : array {
        return [
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getReferenceCacheKeys() : array {
        return array_merge([
        ],
        $this->getCacheKeys());
    }
}
