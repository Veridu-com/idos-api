<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\CompanyProfile;

use App\Entity\User;
use App\Event\AbstractEvent;

/**
 * Deleted event.
 */
class Deleted extends AbstractEvent {
    /**
     * Event related profile.
     *
     * @var App\Entity\User
     */
    public $user;

    /**
     * Class constructor.
     *
     * @param App\Entity\User $user
     *
     * @return void
     */
    public function __construct(User $user) {
        $this->companyProfile = $user;
    }
}
