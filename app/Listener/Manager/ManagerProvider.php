<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Listener\Manager;

use App\Event;
use App\Listener;
use Interop\Container\ContainerInterface;
use App\Listener\AbstractListenerProvider;

class ManagerProvider extends AbstractListenerProvider {
    public function __construct(ContainerInterface $container) {
        $repositoryFactory = $container->get('repositoryFactory');
        $eventFactory = $container->get('eventFactory');
        $emitter = $container->get('eventEmitter');
        $gearmanClient = $container->get('gearmanClient');

        $this->events = [
            Event\Manager\UnhandledEvent::class => [
                new Listener\LogFiredEventListener($container->get('log')('Event'))
            ],
            Event\Profile\Source\Created::class => [
                new ScrapeEventListener(
                    $repositoryFactory->create('Company\Credential'),
                    $repositoryFactory->create('ServiceHandler'),
                    $repositoryFactory->create('Company\Setting'), 
                    $eventFactory,
                    $emitter,
                    $gearmanClient
                )
            ]
        ];
    }
}
