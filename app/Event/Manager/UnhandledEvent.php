<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Manager;

use App\Event\AbstractEvent;
use League\Event\EventInterface;

/**
 * UnhandledEvent event.
 */
class UnhandledEvent extends AbstractEvent {
    /**
     * Event related event.
     *
     * @var \League\Event\EventInterface
     */
    public $event;

    /**
     * Class constructor.
     *
     * @param \League\Event\EventInterface $event
     *
     * @return void
     */
    public function __construct(EventInterface $event) {
        $this->event = $event;
    }
}
