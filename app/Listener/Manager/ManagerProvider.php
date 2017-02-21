<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Listener\Manager;

use App\Event\Manager;
use App\Listener;
use App\Listener\AbstractListenerProvider;
use Interop\Container\ContainerInterface;

class ManagerProvider extends AbstractListenerProvider {
    /**
     * Class constructor.
     *
     * @param \Interop\Container\ContainerInterface $container
     *
     * @return void
     */
    public function __construct(ContainerInterface $container) {
        $eventLogger = ($container->get('log'))('Event');

        $this->events = [
            Manager\UnhandledEvent::class => [
                new Listener\LogFiredEventListener($eventLogger)
            ],

            Manager\WorkQueued::class => [
                new Listener\LogFiredEventListener($eventLogger)
            ]
        ];
    }
}
