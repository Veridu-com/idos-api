<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Entity;

/**
 * Role Entity.
 *
 * @apiEntity schema/role/roleEntity.json
 *
 * @property int        $id
 * @property string     $name
 * @property int        $created_at
 * @FIXME fix comments
 */
class Role extends AbstractEntity {
    /**
     * Company role.
     *
     * @var string
     */
    const COMPANY = 'company';

    /**
     * Company owner role.
     *
     * @var string
     */
    const COMPANY_OWNER = 'company.owner';

    /**
     * Company admin role.
     *
     * @var string
     */
    const COMPANY_ADMIN = 'company.admin';

    /**
     * Company member role.
     *
     * @var string
     */
    const COMPANY_MEMBER = 'company.member';

    /**
     * User role.
     *
     * @var string
     */
    const USER = 'user';

    /**
     * Guest role.
     *
     * @var string
     */
    const GUEST = 'guest';

    /**
     * {@inheritdoc}
     */
    protected $visible = ['name', 'created_at'];

    /**
     * {@inheritdoc}
     */
    protected $dates = ['created_at'];
}
