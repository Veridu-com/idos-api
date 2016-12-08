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

    /**
     * Returns a string representation of the object.
     *
     * @return     string  String representation of the object.
     */
    public function __toString() {
        return sprintf('%s -> %s', get_class($this), get_class($this->event));
    }
}
