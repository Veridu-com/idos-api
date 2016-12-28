<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Listener\Profile;

use App\Event\Profile\Score;
use App\Listener;
use Interop\Container\ContainerInterface;

class ScoreProvider extends Listener\AbstractListenerProvider {
    public function __construct(ContainerInterface $container) {
        $eventLogger       = ($container->get('log'))('Event');
        $commandBus        = $container->get('commandBus');
        $commandFactory    = $container->get('commandFactory');
        $repositoryFactory = $container->get('repositoryFactory');

        $credentialRepository     = $repositoryFactory->create('Company\Credential');
        $settingRepository        = $repositoryFactory->create('Company\Setting');
        $userRepository           = $repositoryFactory->create('User');
        $serviceRepository        = $repositoryFactory->create('Service');

        $eventFactory  = $container->get('eventFactory');
        $emitter       = $container->get('eventEmitter');
        $gearmanClient = $container->get('gearmanClient');

        $this->events = [
            Score\Created::class => [
                new Listener\LogFiredEventListener($eventLogger),
                new Listener\MetricEventListener($commandBus, $commandFactory)
            ],
            Score\Updated::class => [
                new Listener\LogFiredEventListener($eventLogger),
                new Listener\MetricEventListener($commandBus, $commandFactory)
            ],
            Score\Deleted::class => [
                new Listener\LogFiredEventListener($eventLogger),
                new Listener\MetricEventListener($commandBus, $commandFactory)
            ],
            Score\DeletedMulti::class => [
                new Listener\LogFiredEventListener($eventLogger),
                new Listener\MetricEventListener($commandBus, $commandFactory)
            ]
        ];
    }
}
