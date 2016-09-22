<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\User\RoleAccess;

use App\Entity\User\RoleAccess;
use App\Event\AbstractEvent;

/**
 * Deleted event.
 */
class Deleted extends AbstractEvent {
    /**
     * Event related RoleAccess.
     *
     * @var App\Entity\User\RoleAccess
     */
    public $roleAccess;

    /**
     * Class constructor.
     *
     * @param App\Entity\User\RoleAccess $roleAccess
     *
     * @return void
     */
    public function __construct(RoleAccess $roleAccess) {
        $this->roleAccess = $roleAccess;
    }
}
