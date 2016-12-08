<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Company\Invitation;

use App\Entity\Company\Invitation;
use App\Entity\Identity;
use App\Event\AbstractEvent;

/**
 * Deleted event.
 */
class Deleted extends AbstractEvent {
    /**
     * Event related Invitation.
     *
     * @var \App\Entity\Company\Invitation
     */
    public $invitation;
    /**
     * Event related Identity.
     *
     * @var \App\Entity\Identity
     */
    public $identity;

    /**
     * Class constructor.
     *
     * @param \App\Entity\Company\Invitation $invitation
     * @param \App\Entity\Identity           $identity
     *
     * @return void
     */
    public function __construct(Invitation $invitation, Identity $identity) {
        $this->invitation = $invitation;
        $this->identity   = $identity;
    }
}
