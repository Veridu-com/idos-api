<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Company\Permission;

use App\Event\AbstractEvent;
use Illuminate\Support\Collection;

/**
 * Deleted event for multiple permissions.
 */
class DeletedMulti extends AbstractEvent {
    /**
     * Event related Permissions.
     *
     * @var \Illuminate\Support\Collection
     */
    public $permissions;

    /**
     * Class constructor.
     *
     * @param \Illuminate\Support\Collection $permissions
     *
     * @return void
     */
    public function __construct(Collection $permissions) {
        $this->permissions = $permissions;
    }
}
