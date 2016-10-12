<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Company\Member;

use App\Entity\Company\Member;
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
     * Class constructor.
     *
     * @param \App\Entity\Company\Member $member
     *
     * @return void
     */
    public function __construct(Member $member) {
        $this->member = $member;
    }
}
