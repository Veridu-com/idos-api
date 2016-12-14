<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Listener\Profile;

use App\Event\Profile\Raw;
use App\Listener;
use App\Listener\Manager\QueueServiceTaskListener;
use Interop\Container\ContainerInterface;

class RawProvider extends Listener\AbstractListenerProvider {
    public function __construct(ContainerInterface $container) {
        $repositoryFactory        = $container->get('repositoryFactory');
        $credentialRepository     = $repositoryFactory->create('Company\Credential');
        $serviceRepository = $repositoryFactory->create('Service');

        $eventFactory  = $container->get('eventFactory');
        $emitter       = $container->get('eventEmitter');
        $gearmanClient = $container->get('gearmanClient');

        $eventLogger    = ($container->get('log'))('Event');
        $commandBus     = $container->get('commandBus');
        $commandFactory = $container->get('commandFactory');

        $this->events = [
            Raw\Created::class => [
                new Listener\LogFiredEventListener($eventLogger),
                new QueueServiceTaskListener(
                    $credentialRepository,
                    $serviceRepository,
                    $eventFactory,
                    $emitter,
                    $gearmanClient
                ),
                new Listener\MetricEventListener($commandBus, $commandFactory)
            ],
            Raw\Updated::class => [
                new Listener\LogFiredEventListener($eventLogger),
                new QueueServiceTaskListener(
                    $credentialRepository,
                    $serviceRepository,
                    $eventFactory,
                    $emitter,
                    $gearmanClient
                ),
                new Listener\MetricEventListener($commandBus, $commandFactory)
            ],
            Raw\Deleted::class => [
                new Listener\LogFiredEventListener($eventLogger),
                new Listener\MetricEventListener($commandBus, $commandFactory)
            ],
            Raw\DeletedMulti::class => [
                new Listener\LogFiredEventListener($eventLogger),
                new Listener\MetricEventListener($commandBus, $commandFactory)
            ]
        ];
    }
}
