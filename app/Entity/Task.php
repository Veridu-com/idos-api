<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Entity;

use App\Extension\SecureFields;
use App\Helper\Utils;

/**
 * Task Entity.
 *
 * @apiEntity schema/user/processEntity.json
 *
 * @property int     $id
 * @property string  $name
 * @property string  $event
 * @property string  $message
 * @property boolean $running
 * @property boolean $success
 * @property int     $process_id
 * @property int     $created_at
 * @property int     $updated_at
 */
class Task extends AbstractEntity {
    use SecureFields;
    /**
     * {@inheritdoc}
     */
    const CACHE_PREFIX = 'Task';

    /**
     * {@inheritdoc}
     */
    protected $visible = ['id', 'name', 'event', 'running', 'success', 'process_id', 'created_at', 'updated_at'];
    /**
     * {@inheritdoc}
     */
    protected $dates = ['created_at', 'updated_at'];
    /**
     * The attributes that should be secured.
     *
     * @var array
     */
    protected $secure = ['message'];

    /**
     * {@inheritdoc}
     */
    public function getCacheKeys() : array {
        return [
            sprintf(
                '%s.id.%s',
                self::CACHE_PREFIX,
                $this->id
            )
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getReferenceCacheKeys() : array {
        return array_merge(
            [
                sprintf(
                    '%s.by.process_id.%s',
                    self::CACHE_PREFIX,
                    $this->userId
                )
            ],
            $this->getCacheKeys()
        );
    }
}
