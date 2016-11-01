<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Listener\Manager;

use App\Event;
use App\Listener;
use App\Listener\AbstractListenerProvider;
use Interop\Container\ContainerInterface;

class ManagerProvider extends AbstractListenerProvider {
    public function __construct(ContainerInterface $container) {
        $repositoryFactory        = $container->get('repositoryFactory');
        $credentialRepository     = $repositoryFactory->create('Company\Credential');
        $settingRepository        = $repositoryFactory->create('Company\Setting');
        $serviceHandlerRepository = $repositoryFactory->create('ServiceHandler');

        $eventFactory  = $container->get('eventFactory');
        $emitter       = $container->get('eventEmitter');
        $gearmanClient = $container->get('gearmanClient');

        $eventLogger    = ($container->get('log'))('Event');

        $this->events = [
            Event\Manager\UnhandledEvent::class => [
                new Listener\LogFiredEventListener($eventLogger)
            ],

            Event\Manager\WorkQueued::class => [
                new Listener\LogFiredEventListener($eventLogger)
            ],

            // Source created triggers Manager\Scrape event listener
            Event\Profile\Source\Created::class => [
                new Listener\LogFiredEventListener($eventLogger),
                new ScrapeEventListener($credentialRepository, $serviceHandlerRepository, $settingRepository, $eventFactory, $emitter, $gearmanClient)
            ],

            // Raw created triggers Service Task listener
            Event\Profile\Raw\Created::class => [
                new Listener\LogFiredEventListener($eventLogger),
                new QueueServiceTaskListener($credentialRepository, $serviceHandlerRepository, $eventFactory, $emitter, $gearmanClient)
            ],
            Event\Profile\Raw\Updated::class => [
                new Listener\LogFiredEventListener($eventLogger),
                new QueueServiceTaskListener($credentialRepository, $serviceHandlerRepository, $eventFactory, $emitter, $gearmanClient)
            ],

            // Feature created triggers Service Task listener
            Event\Profile\Feature\Created::class => [
                new Listener\LogFiredEventListener($eventLogger),
                new QueueServiceTaskListener($credentialRepository, $serviceHandlerRepository, $eventFactory, $emitter, $gearmanClient)
            ],
            Event\Profile\Feature\Updated::class => [
                new Listener\LogFiredEventListener($eventLogger),
                new QueueServiceTaskListener($credentialRepository, $serviceHandlerRepository, $eventFactory, $emitter, $gearmanClient)
            ],
            Event\Profile\Feature\CreatedBulk::class => [
                new Listener\LogFiredEventListener($eventLogger),
                new QueueServiceTaskListener($credentialRepository, $serviceHandlerRepository, $eventFactory, $emitter, $gearmanClient)
            ],
            // Task Updated created triggers Service Task listener
            // @FIXME Talk to Flávio if we need this granularity
            // task [
            //  'onUpdated': [ event1, event2]
            //  'onCompleted': [ event3, event4]
            // ]
            // Uses: Scraper calling other servies.
            //
            // Event\Profile\Task\Updated::class => [
            //     new QueueServiceTaskListener($credentialRepository, $serviceHandlerRepository, $eventFactory, $emitter, $gearmanClient)
            // ],

            // Task Completed created triggers Service Task listener
            Event\Profile\Task\Completed::class => [
                new Listener\LogFiredEventListener($eventLogger),
                new QueueServiceTaskListener($credentialRepository, $serviceHandlerRepository, $eventFactory, $emitter, $gearmanClient)
            ],

        ];
    }
}
