<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Permission;

use App\Entity\Permission;
use App\Event\AbstractEvent;

/**
 * Deleted event.
 */
class Deleted extends AbstractEvent {
    /**
     * Event related Permission.
     *
     * @var App\Entity\Permission
     */
    public $permission;

    /**
     * Class constructor.
     *
     * @param App\Entity\Permission $permission
     *
     * @return void
     */
    public function __construct(Permission $permission) {
        $this->permission = $permission;
    }
}
