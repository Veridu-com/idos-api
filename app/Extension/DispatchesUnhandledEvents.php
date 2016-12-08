<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Extension;

use App\Factory\Event as EventFactory;
use League\Event\Emitter;
use League\Event\EventInterface;

/**
 * Sends Unhandled Events.
 */
trait DispatchesUnhandledEvents {
    /**
     * Dispatches an unhandled event.
     *
     * @param \League\Event\EventInterface $event        The event
     * @param EventFactory                 $eventFactory The event factory
     * @param \League\Event\Emitter        $emitter      The emitter
     */
    private function dispatchUnhandledEvent(EventInterface $event, EventFactory $eventFactory, Emitter $emitter) {
        $unhandledEvent = $eventFactory->create(
            'Manager\\UnhandledEvent',
            $event
        );

        $emitter->emit($unhandledEvent);
    }
}
