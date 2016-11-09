<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Company\Member;

use App\Entity\Company\Member;
use App\Entity\Identity;
use App\Event\AbstractEvent;

/**
 * Updated event.
 */
class Updated extends AbstractEvent {
    /**
     * Event related Member.
     *
     * @var \App\Entity\Company\Member
     */
    public $member;
    /**
     * Event related Identity.
     *
     * @var \App\Entity\Identity
     */
    public $identity;

    /**
     * Class constructor.
     *
     * @param \App\Entity\Company\Member $member
     * @param \App\Entity\Identity $identity
     *
     * @return void
     */
    public function __construct(Member $member, Identity $identity) {
        $this->member = $member;
        $this->identity = $identity;
    }
}
