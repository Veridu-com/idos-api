<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\User;

use App\Entity\User;
use App\Event\AbstractEvent;

/**
 * Created event.
 */
class Created extends AbstractEvent {
    /**
     * Event related User.
     *
     * @var \App\Entity\User
     */
    public $user;

    /**
     * Class constructor.
     *
     * @param \App\Entity\User $user
     *
     * @return void
     */
    public function __construct(User $user) {
        $this->user = $user;
    }
}
