<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Company\Hook;

use App\Entity\Company\Hook;
use App\Event\AbstractEvent;

/**
 * Deleted event.
 */
class Deleted extends AbstractEvent {
    /**
     * Event related Hook.
     *
     * @var \App\Entity\Company\Hook
     */
    public $hook;

    /**
     * Class constructor.
     *
     * @param \App\Entity\Company\Hook $hook
     *
     * @return void
     */
    public function __construct(Hook $hook) {
        $this->hook = $hook;
    }
}
