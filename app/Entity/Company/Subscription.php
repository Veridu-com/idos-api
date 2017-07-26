<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Entity\Company;

use App\Entity\AbstractEntity;

/**
 * Credentials Entity.
 *
 * @apiEntity schema/subscription/subscriptionEntity.json
 *
 * @property int    $id
 * @property int    $gate_id
 * @property string $category_name
 * @property int    $credential_id
 * @property int    $identity_id
 * @property int    $created_at
 * @property int    $updated_at
 */
class Subscription extends AbstractEntity {
    /**
     * {@inheritdoc}
     */
    protected $visible = ['id', 'category_name', 'created_at', 'updated_at'];
    /**
     * {@inheritdoc}
     */
    protected $dates = ['created_at', 'updated_at'];

    /**
     * {@inheritdoc}
     */
    public $relationships = [
        'category' => 'Category'
    ];
}
