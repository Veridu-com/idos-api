<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Entity\Profile;

use App\Entity\AbstractEntity;

/**
 * Candidate Entity.
 *
 * @apiEntity schema/candidate/candidateEntity.json
 *
 * @property int    $id
 * @property string $attribute
 * @property string $value
 * @property float  $support
 * @property int    $created_at
 * @property int    $updated_at
 */
class Candidate extends AbstractEntity {
    /**
     * {@inheritdoc}
     */
    protected $visible = ['attribute', 'value', 'support', 'created_at'];

    /**
     * {@inheritdoc}
     */
    protected $cast = ['support' => 'float'];

    /**
     * {@inheritdoc}
     */
    protected $dates = ['created_at'];

    /**
     * {@inheritdoc}
     */
    protected $secure = ['value'];

    /**
     * {@inheritdoc}
     */
    public $relationships = [
        'creator' => 'Handler'
    ];
}
