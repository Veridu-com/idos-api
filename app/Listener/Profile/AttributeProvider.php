<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Listener\Profile;

use App\Event\Profile\Attribute;
use App\Listener;
use Interop\Container\ContainerInterface;

class AttributeProvider extends Listener\AbstractListenerProvider {
    public function __construct(ContainerInterface $container) {
        $this->events = [
            Attribute\Created::class => [
                new Listener\LogFiredEventListener(($container->get('log'))('Event')),
                new Listener\MetricEventListener($container->get('commandBus'), $container->get('commandFactory'))
            ],
            Attribute\Updated::class => [
                new Listener\LogFiredEventListener(($container->get('log'))('Event')),
                new Listener\MetricEventListener($container->get('commandBus'), $container->get('commandFactory'))
            ],
            Attribute\Deleted::class => [
                new Listener\LogFiredEventListener(($container->get('log'))('Event')),
                new Listener\MetricEventListener($container->get('commandBus'), $container->get('commandFactory'))
            ],
            Attribute\DeletedMulti::class => [
                new Listener\LogFiredEventListener(($container->get('log'))('Event')),
                new Listener\MetricEventListener($container->get('commandBus'), $container->get('commandFactory'))
            ]
        ];
    }
}
