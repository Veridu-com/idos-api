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
 * Created event.
 */
class Created extends AbstractEvent {
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
    public $actor;

    /**
     * Class constructor.
     *
     * @param \App\Entity\Company\Hook $hook
     *
     * @return void
     */
    public function __construct(Hook $hook, Identity $actor) {
        $this->hook  = $hook;
        $this->actor = $actor;
    }
}
