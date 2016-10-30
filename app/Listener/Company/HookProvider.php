<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Listener\Company;

use App\Event\Company\Hook;
use App\Listener;
use Interop\Container\ContainerInterface;

class HookProvider extends Listener\AbstractListenerProvider {
    public function __construct(ContainerInterface $container) {
        $this->events = [
            Hook\Created::class => [
                new Listener\LogFiredEventListener($container->get('commandBus'), $container->get('commandFactory'), $container->get('log')('Event'))
            ],
            Hook\Updated::class => [
                new Listener\LogFiredEventListener($container->get('commandBus'), $container->get('commandFactory'), $container->get('log')('Event'))
            ],
            Hook\Deleted::class => [
                new Listener\LogFiredEventListener($container->get('commandBus'), $container->get('commandFactory'), $container->get('log')('Event'))
            ],
            Hook\DeletedMulti::class => [
                new Listener\LogFiredEventListener($container->get('commandBus'), $container->get('commandFactory'), $container->get('log')('Event'))
            ]
        ];
    }
}
