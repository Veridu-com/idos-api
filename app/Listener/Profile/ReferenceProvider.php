<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Listener\Profile;

use App\Event\Profile\Reference;
use App\Listener;
use Interop\Container\ContainerInterface;

class ReferenceProvider extends Listener\AbstractListenerProvider {
  /**
   * Class constructor.
   *
   * @param \Interop\Container\ContainerInterface  $container
   *
   * @return void
   */
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
            Reference\Created::class => [
                new Listener\LogFiredEventListener($eventLogger),
                $evaluateRecommendationListener,
                new Listener\MetricEventListener($commandBus, $commandFactory)
            ],
            Reference\Updated::class => [
                new Listener\LogFiredEventListener($eventLogger),
                $evaluateRecommendationListener,
                new Listener\MetricEventListener($commandBus, $commandFactory)
            ],
            Reference\Deleted::class => [
                new Listener\LogFiredEventListener($eventLogger),
                $evaluateRecommendationListener,
                new Listener\MetricEventListener($commandBus, $commandFactory)
            ],
            Reference\DeletedMulti::class => [
                new Listener\LogFiredEventListener($eventLogger),
                $evaluateRecommendationListener,
                new Listener\MetricEventListener($commandBus, $commandFactory)
            ]
        ];
    }
}
