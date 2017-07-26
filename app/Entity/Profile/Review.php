<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Entity\Profile;

use App\Entity\AbstractEntity;

/**
 * Review Entity.
 *
 * @apiEntity schema/review/reviewEntity.json
 *
 * @property int    $id
 * @property bool   $positive
 * @property string $description
 * @property int    $created_at
 * @property int    $updated_at
 */
class Review extends AbstractEntity {
    /**
     * {@inheritdoc}
     */
    protected $visible = ['id', 'positive', 'description', 'gate_id', 'recommendation_id', 'user_id', 'created_at'];
    /**
     * {@inheritdoc}
     */
    protected $dates = ['created_at', 'updated_at'];
    /**
     * {@inheritdoc}
     */
    protected $obfuscated = ['id', 'gate_id', 'recommendation_id', 'user_id'];
}
