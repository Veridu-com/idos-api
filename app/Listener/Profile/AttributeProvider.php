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

        // Listeners
        $evaluateRecommendationListener = new Listener\Profile\Recommendation\EvaluateRecommendationListener(
            $settingRepository,
            $serviceRepository,
            $userRepository,
            $eventLogger,
            $eventFactory,
            $emitter,
            $gearmanClient
        );

        $this->events = [
            Attribute\Created::class => [
                new Listener\LogFiredEventListener($eventLogger),
                $evaluateRecommendationListener,
                new Listener\MetricEventListener($commandBus, $commandFactory)
            ],
            Attribute\Updated::class => [
                new Listener\LogFiredEventListener($eventLogger),
                $evaluateRecommendationListener,
                new Listener\MetricEventListener($commandBus, $commandFactory)
            ],
            Attribute\UpsertedBulk::class => [
                new Listener\LogFiredEventListener($eventLogger),
                $evaluateRecommendationListener,
                // @FIXME talk with team about it.
                // new Listener\MetricEventListener($commandBus, $commandFactory)
            ],
            Attribute\Deleted::class => [
                new Listener\LogFiredEventListener($eventLogger),
                $evaluateRecommendationListener,
                new Listener\MetricEventListener($commandBus, $commandFactory)
            ],
            Attribute\DeletedMulti::class => [
                new Listener\LogFiredEventListener($eventLogger),
                $evaluateRecommendationListener,
                new Listener\MetricEventListener($commandBus, $commandFactory)
            ]
        ];
    }
}
