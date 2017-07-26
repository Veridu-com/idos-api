<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Provider;

use App\Event\Manager\UnhandledEvent;
use App\Event\Manager\WorkQueued;
use App\Listener\EventLogger;
use Interop\Container\ContainerInterface;
use Refinery29\Event\LazyListener;

class Manager extends AbstractProvider {
    /**
     * Class constructor.
     *
     * @param \Interop\Container\ContainerInterface $container
     *
     * @return void
     */
    public function __construct(ContainerInterface $container) {
        $this->events = [
            UnhandledEvent::class => [
                LazyListener::fromAlias(
                    EventLogger::class,
                    $container
                )
            ],

            WorkQueued::class => [
                LazyListener::fromAlias(
                    EventLogger::class,
                    $container
                )
            ]
        ];
    }
}
