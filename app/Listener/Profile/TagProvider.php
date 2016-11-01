<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Listener\Profile;

use App\Event\Profile\Tag;
use App\Listener;
use Interop\Container\ContainerInterface;

class TagProvider extends Listener\AbstractListenerProvider {
    public function __construct(ContainerInterface $container) {
        $this->events = [
            Tag\Created::class => [
                new Listener\LogFiredEventListener(($container->get('log'))('Event')),
                new Listener\MetricEventListener($container->get('commandBus'), $container->get('commandFactory'))
            ],
            Tag\Updated::class => [
                new Listener\LogFiredEventListener(($container->get('log'))('Event')),
                new Listener\MetricEventListener($container->get('commandBus'), $container->get('commandFactory'))
            ],
            Tag\Deleted::class => [
                new Listener\LogFiredEventListener(($container->get('log'))('Event')),
                new Listener\MetricEventListener($container->get('commandBus'), $container->get('commandFactory'))
            ],
            Tag\DeletedMulti::class => [
                new Listener\LogFiredEventListener(($container->get('log'))('Event')),
                new Listener\MetricEventListener($container->get('commandBus'), $container->get('commandFactory'))
            ]
        ];
    }
}
