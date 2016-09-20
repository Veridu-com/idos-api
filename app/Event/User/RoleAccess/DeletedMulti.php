<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\User\RoleAccess;

use App\Event\AbstractEvent;
use Illuminate\Support\Collection;

/**
 * Deleted event for multiple roleAccesses.
 */
class DeletedMulti extends AbstractEvent {
    /**
     * Event related Role Acesses.
     *
     * @var \Illuminate\Support\Collection
     */
    public $roleAccesses;

    /**
     * Class constructor.
     *
     * @param \Illuminate\Support\Collection $roleAccesses
     *
     * @return void
     */
    public function __construct(Collection $roleAccesses) {
        $this->roleAccesses = $roleAccesses;
    }
}
