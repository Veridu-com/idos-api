<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Listener\Profile;

use App\Event\Profile\Gate;
use App\Listener;
use App\Listener\Manager\QueueServiceTaskListener;
use Interop\Container\ContainerInterface;

class GateProvider extends Listener\AbstractListenerProvider {
    public function __construct(ContainerInterface $container) {
        $eventLogger    = ($container->get('log'))('Event');
        $commandBus     = $container->get('commandBus');
        $commandFactory = $container->get('commandFactory');
        $repositoryFactory = $container->get('repositoryFactory');

        $credentialRepository     = $repositoryFactory->create('Company\Credential');
        $settingRepository        = $repositoryFactory->create('Company\Setting');
        $userRepository           = $repositoryFactory->create('User');
        $serviceHandlerRepository = $repositoryFactory->create('ServiceHandler');

        $eventFactory  = $container->get('eventFactory');
        $emitter       = $container->get('eventEmitter');
        $gearmanClient = $container->get('gearmanClient');

        // Listeners
        $evaluateRecommendationListener = new Listener\Profile\Recommendation\EvaluateRecommendationListener(
            $settingRepository, 
            $serviceHandlerRepository, 
            $userRepository, 
            $eventLogger, 
            $eventFactory, 
            $emitter,
            $gearmanClient
        );

        $queueServiceTaskListener = new QueueServiceTaskListener(
            $credentialRepository,
            $serviceHandlerRepository,
            $eventFactory,
            $emitter,
            $gearmanClient
        );

        $this->events = [
            Gate\Created::class => [
                new Listener\LogFiredEventListener($eventLogger),
                $evaluateRecommendationListener,
                $queueServiceTaskListener,
                new Listener\MetricEventListener($commandBus, $commandFactory)
            ],
            Gate\Updated::class => [
                new Listener\LogFiredEventListener($eventLogger),
                $evaluateRecommendationListener,
                new Listener\MetricEventListener($commandBus, $commandFactory)
            ],
            Gate\Deleted::class => [
                new Listener\LogFiredEventListener($eventLogger),
                $evaluateRecommendationListener,
                new Listener\MetricEventListener($commandBus, $commandFactory)
            ],
            Gate\DeletedMulti::class => [
                new Listener\LogFiredEventListener($eventLogger),
                $evaluateRecommendationListener,
                new Listener\MetricEventListener($commandBus, $commandFactory)
            ]
        ];
    }
}
