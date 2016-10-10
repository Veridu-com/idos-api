<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Company\Member;

use App\Entity\Company\Invitation;
use App\Event\AbstractEvent;

/**
 * InvitationCreated event.
 */
class InvitationCreated extends AbstractEvent {
    /**
     * Event related Member.
     *
     * @var App\Entity\Company\Invitation
     */
    public $invitation;

    /**
     * Class constructor.
     *
     * @param App\Entity\Company\Member $member
     *
     * @return void
     */
    public function __construct(Invitation $invitation) {
        $this->invitation = $invitation;
    }
}
