<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\RoleAccess;

use App\Entity\RoleAccess;
use App\Event\AbstractEvent;

/**
 * Created event.
 */
class Created extends AbstractEvent {
    /**
     * Event related RoleAccess.
     *
     * @var App\Entity\RoleAccess
     */
    public $roleAccess;

    /**
     * Class constructor.
     *
     * @param App\Entity\RoleAccess $roleAccess
     *
     * @return void
     */
    public function __construct(RoleAccess $roleAccess) {
        $this->roleAccess = $roleAccess;
    }
}
