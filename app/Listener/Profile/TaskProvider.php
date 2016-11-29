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
        $repositoryFactory        = $container->get('repositoryFactory');
        $credentialRepository     = $repositoryFactory->create('Company\Credential');
        $serviceHandlerRepository = $repositoryFactory->create('ServiceHandler');

        $eventFactory  = $container->get('eventFactory');
        $emitter       = $container->get('eventEmitter');
        $gearmanClient = $container->get('gearmanClient');

        $eventLogger    = ($container->get('log'))('Event');
        $commandBus     = $container->get('commandBus');
        $commandFactory = $container->get('commandFactory');

        $this->events = [
            Task\Created::class => [
                new Listener\LogFiredEventListener($eventLogger),
                new Listener\MetricEventListener($commandBus, $commandFactory)
            ],
            // @FIXME Talk to Flavio if we need this granularity
            // task [
            //  'onUpdated': [ event1, event2]
            //  'onCompleted': [ event3, event4]
            // ]
            // Uses: Scraper calling other servies.
            Task\Updated::class => [
                new Listener\LogFiredEventListener($eventLogger),
                new Listener\MetricEventListener($commandBus, $commandFactory)
            ],
            Task\Completed::class => [
                new Listener\LogFiredEventListener($eventLogger),
                new QueueServiceTaskListener(
                    $credentialRepository,
                    $serviceHandlerRepository,
                    $eventFactory,
                    $emitter,
                    $gearmanClient
                )
            ]
        ];
    }
}
