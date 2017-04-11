<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Listener\Profile;

use App\Event\Profile\Candidate;
use App\Listener;
use Interop\Container\ContainerInterface;

class CandidateProvider extends Listener\AbstractListenerProvider {
    /**
     * Class constructor.
     *
     * @param \Interop\Container\ContainerInterface $container
     *
     * @return void
     */
    public function __construct(ContainerInterface $container) {
        $repositoryFactory   = $container->get('repositoryFactory');
        $candidateRepository = $repositoryFactory->create('Profile\Candidate');
        $featureRepository   = $repositoryFactory->create('Profile\Feature');

        $eventLogger    = ($container->get('log'))('Event');
        $commandBus     = $container->get('commandBus');
        $commandFactory = $container->get('commandFactory');

        $this->events = [
            Candidate\Created::class => [
                new Listener\LogFiredEventListener($eventLogger),
                new Listener\MetricEventListener($commandBus, $commandFactory),
                new Listener\Profile\AttributeListener(
                    $candidateRepository,
                    $featureRepository,
                    $commandBus,
                    $commandFactory
                )
            ],
            Candidate\Updated::class => [
                new Listener\LogFiredEventListener($eventLogger),
                new Listener\MetricEventListener($commandBus, $commandFactory),
                new Listener\Profile\AttributeListener(
                    $candidateRepository,
                    $featureRepository,
                    $commandBus,
                    $commandFactory
                )
            ],
            Candidate\Deleted::class => [
                new Listener\LogFiredEventListener($eventLogger),
                new Listener\MetricEventListener($commandBus, $commandFactory),
                new Listener\Profile\AttributeListener(
                    $candidateRepository,
                    $featureRepository,
                    $commandBus,
                    $commandFactory
                )
            ],
            Candidate\DeletedMulti::class => [
                new Listener\LogFiredEventListener($eventLogger),
                new Listener\MetricEventListener($commandBus, $commandFactory),
                new Listener\Profile\AttributeListener(
                    $candidateRepository,
                    $featureRepository,
                    $commandBus,
                    $commandFactory
                )
            ]
        ];
    }
}
