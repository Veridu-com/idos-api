<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Entity\Profile;

use App\Entity\AbstractEntity;

/**
 * Recommendation Entity.
 *
 * @apiEntity schema/recommendation/recommendationEntity.json
 *
 * @property int    $id
 * @property int    $creator
 * @property int    $user_id
 * @property string $result
 * @property string $passed
 * @property string $failed
 * @property int    $created_at
 * @property int    $updated_at
 */
class Recommendation extends AbstractEntity {
    /**
     * {@inheritdoc}
     */
    protected $visible = [
        'result',
        'passed',
        'failed',
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
    protected $json = ['passed', 'failed'];
}
