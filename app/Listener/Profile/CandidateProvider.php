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
    public function __construct(ContainerInterface $container) {
        $this->events = [
            Candidate\Created::class => [
                new Listener\LogFiredEventListener(($container->get('log'))('Event')),
                new Listener\MetricEventListener($container->get('commandBus'), $container->get('commandFactory')),
                new Listener\Profile\AttributeListener(
                    $container
                        ->get('repositoryFactory')
                        ->create('Profile\\Candidate'),
                    $container
                        ->get('repositoryFactory')
                        ->create('Profile\\Feature'),
                    $container
                        ->get('commandBus'),
                    $container
                        ->get('commandFactory')
                )
            ],
            Candidate\Updated::class => [
                new Listener\LogFiredEventListener(($container->get('log'))('Event')),
                new Listener\MetricEventListener($container->get('commandBus'), $container->get('commandFactory')),
                new Listener\Profile\AttributeListener(
                    $container
                        ->get('repositoryFactory')
                        ->create('Profile\\Candidate'),
                    $container
                        ->get('repositoryFactory')
                        ->create('Profile\\Feature'),
                    $container
                        ->get('commandBus'),
                    $container
                        ->get('commandFactory')
                )
            ],
            Candidate\Deleted::class => [
                new Listener\LogFiredEventListener(($container->get('log'))('Event')),
                new Listener\MetricEventListener($container->get('commandBus'), $container->get('commandFactory')),
                new Listener\Profile\AttributeListener(
                    $container
                        ->get('repositoryFactory')
                        ->create('Profile\\Candidate'),
                    $container
                        ->get('repositoryFactory')
                        ->create('Profile\\Feature'),
                    $container
                        ->get('commandBus'),
                    $container
                        ->get('commandFactory')
                )
            ],
            Candidate\DeletedMulti::class => [
                new Listener\LogFiredEventListener(($container->get('log'))('Event')),
                new Listener\MetricEventListener($container->get('commandBus'), $container->get('commandFactory')),
                new Listener\Profile\AttributeListener(
                    $container
                        ->get('repositoryFactory')
                        ->create('Profile\\Candidate'),
                    $container
                        ->get('repositoryFactory')
                        ->create('Profile\\Feature'),
                    $container
                        ->get('commandBus'),
                    $container
                        ->get('commandFactory')
                )
            ]
        ];
    }
}
