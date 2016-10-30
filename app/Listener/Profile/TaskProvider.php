<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Listener\Profile;

use App\Event\Profile\Task;
use App\Listener;
use Interop\Container\ContainerInterface;

class TaskProvider extends Listener\AbstractListenerProvider {
    public function __construct(ContainerInterface $container) {
        $this->events = [
            Task\Created::class => [
                new Listener\LogFiredEventListener($container->get('commandBus'), $container->get('commandFactory'), $container->get('log')('Event'))
            ],
            Task\Updated::class => [
                new Listener\LogFiredEventListener($container->get('commandBus'), $container->get('commandFactory'), $container->get('log')('Event'))
            ]
        ];
    }
}
