<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Event\Hook;

use App\Entity\Hook;
use App\Event\AbstractEvent;

/**
 * Updated event.
 */
class Updated extends AbstractEvent {
    /**
     * Event related Hook.
     *
     * @var App\Entity\Hook
     */
    public $hook;

    /**
     * Class constructor.
     *
     * @param App\Entity\Hook $hook
     *
     * @return void
     */
    public function __construct(Hook $hook) {
        $this->hook = $hook;
    }
}
