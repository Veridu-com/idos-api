<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Entity;

use App\Extension\SlugMutator;

/**
 * Companies Entity.
 *
 * @apiEntity schema/company/companyEntity.json
 *
 * @property int    $id
 * @property string $name
 * @property string $slug
 * @property string $public_key
 * @property string $private_key
 * @property int    $created_at
 * @property int    $updated_at
 */
class Company extends AbstractEntity {
    use SlugMutator;

    /**
     * {@inheritdoc}
     */
    protected $visible = ['name', 'slug', 'public_key', 'created_at', 'updated_at'];
    /**
     * {@inheritdoc}
     */
    protected $dates = ['created_at', 'updated_at'];
    /**
     * {@inheritdoc}
     */
    protected $secure = ['private_key'];
}
