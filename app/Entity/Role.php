<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Entity;

/**
 * Role Entity.
 *
 * @FIXME Schema does not exist!
 * @FIXME @apiEntity schema/role/roleEntity.json
 *
 * @property int        $id
 * @property string     $name
 * @property int        $rank
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
     * Company bit.
     *
     * @var int
     */
    const COMPANY_BIT = 0x01;
    /**
     * Company owner role.
     *
     * @var string
     */
    const COMPANY_OWNER = 'company.owner';
    /**
     * Company owner bit.
     *
     * @var int
     */
    const COMPANY_OWNER_BIT = 0x02;
    /**
     * Company admin role.
     *
     * @var string
     */
    const COMPANY_ADMIN = 'company.admin';
    /**
     * Company admin bit.
     *
     * @var int
     */
    const COMPANY_ADMIN_BIT = 0x04;
    /**
     * Company member role.
     *
     * @var string
     */
    const COMPANY_REVIEWER = 'company.reviewer';
    /**
     * Company member bit.
     *
     * @var int
     */
    const COMPANY_REVIEWER_BIT = 0x08;
    /**
     * User role.
     *
     * @var string
     */
    const USER = 'user';
    /**
     * User bit.
     *
     * @var int
     */
    const COMPANY_USER_BIT = 0x16;
    /**
     * Guest role.
     *
     * @var string
     */
    const GUEST = 'guest';
    /**
     * Guest bit.
     *
     * @var int
     */
    const COMPANY_GUEST_BIT = 0x32;

    /**
     * {@inheritdoc}
     */
    protected $visible = ['name', 'created_at'];
    /**
     * {@inheritdoc}
     */
    protected $dates = ['created_at'];
}
