<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Entity\Company;

use App\Entity\AbstractEntity;
use App\Extension\SlugMutator;

/**
 * Credentials Entity.
 *
 * @apiEntity schema/credential/credentialEntity.json
 *
 * @property int    $id
 * @property string $company_id
 * @property string $name
 * @property string $slug
 * @property string $public
 * @property string $private
 * @property string $production
 * @property int    $created_at
 * @property int    $updated_at
 */
class Credential extends AbstractEntity {
    use SlugMutator;

    /**
     * {@inheritdoc}
     */
    protected $visible = ['name', 'slug', 'public', 'production', 'created_at', 'updated_at'];
    /**
     * {@inheritdoc}
     */
    protected $dates = ['created_at', 'updated_at'];
    /**
     * {@inheritdoc}
     */
    protected $secure = ['private'];
}
