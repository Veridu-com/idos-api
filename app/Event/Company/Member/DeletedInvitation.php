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
 * DeletedInvitation event.
 */
class DeletedInvitation extends AbstractEvent {
    /**
     * Event related Invitation.
     *
     * @var App\Entity\Company\Invitation
     */
    public $invitation;

    /**
     * Class constructor.
     *
     * @param App\Entity\Company\Invitation $invitation
     *
     * @return void
     */
    public function __construct(Invitation $invitation) {
        $this->invitation = $invitation;
    }
}
