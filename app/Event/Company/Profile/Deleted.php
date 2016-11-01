<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Company\Profile;

use App\Entity\User;
use App\Entity\Identity;
use App\Event\AbstractEvent;

/**
 * Deleted event.
 */
class Deleted extends AbstractEvent {
    /**
     * Event related profile.
     *
     * @var \App\Entity\User
     */
    public $user;
    /**
     * Event related Identity.
     *
     * @var \App\Entity\Identity
     */
    public $identity;

    /**
     * Class constructor.
     *
     * @param \App\Entity\User $user
     *
     * @return void
     */
    public function __construct(User $user, Identity $identity) {
        $this->companyProfile = $user;
        $this->identity = $identity;
    }
}
