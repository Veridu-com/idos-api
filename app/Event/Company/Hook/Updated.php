<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Company\Hook;

use App\Entity\Company\Hook;
use App\Entity\Identity;
use App\Event\AbstractEvent;

/**
 * Updated event.
 */
class Updated extends AbstractEvent {
    /**
     * Event related Hook.
     *
     * @var \App\Entity\Company\Hook
     */
    public $hook;
    /**
     * Event related Identity.
     *
     * @var \App\Entity\Identity
     */
    public $identity;

    /**
     * Class constructor.
     *
     * @param \App\Entity\Company\Hook $hook
     * @param \App\Entity\Identity     $identity
     *
     * @return void
     */
    public function __construct(Hook $hook, Identity $identity) {
        $this->hook     = $hook;
        $this->identity = $identity;
    }
}
