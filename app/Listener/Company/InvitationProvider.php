<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Listener\Company;

use App\Event\Company\Invitation;
use App\Listener;
use App\Listener\Manager\QueueServiceTaskListener;
use Interop\Container\ContainerInterface;

class InvitationProvider extends Listener\AbstractListenerProvider {
    public function __construct(ContainerInterface $container) {
        $repositoryFactory        = $container->get('repositoryFactory');
        $credentialRepository     = $repositoryFactory->create('Company\Credential');
        $settingRepository        = $repositoryFactory->create('Company\Setting');
        $serviceHandlerRepository = $repositoryFactory->create('ServiceHandler');

        $eventFactory  = $container->get('eventFactory');
        $emitter       = $container->get('eventEmitter');
        $gearmanClient = $container->get('gearmanClient');

        $eventLogger = $container->get('log')('Event');

        $this->events = [
            Invitation\Created::class => [
                new QueueServiceTaskListener($credentialRepository, $serviceHandlerRepository, $eventFactory, $emitter, $gearmanClient),
                new Listener\LogFiredEventListener($eventLogger)
            ],
            Invitation\Resend::class => [
                new QueueServiceTaskListener($credentialRepository, $serviceHandlerRepository, $eventFactory, $emitter, $gearmanClient),
                new Listener\LogFiredEventListener($eventLogger)
            ],
            Invitation\Updated::class => [
                new Listener\LogFiredEventListener($eventLogger)
            ]
        ];
    }
}
