<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Listener\Profile;

use App\Event\Profile\Recommendation;
use App\Listener;
use Interop\Container\ContainerInterface;

class RecommendationProvider extends Listener\AbstractListenerProvider {
    public function __construct(ContainerInterface $container) {
        $eventLogger    = ($container->get('log'))('Event');
        $commandBus     = $container->get('commandBus');
        $commandFactory = $container->get('commandFactory');

        $this->events = [
            Recommendation\Upserted::class => [
                new Listener\LogFiredEventListener($eventLogger),
                new Listener\MetricEventListener($commandBus, $commandFactory)
            ]
        ];
    }
}
