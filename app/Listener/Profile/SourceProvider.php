<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Listener\Profile;

use App\Event\Profile\Source;
use App\Listener;
use App\Listener\Manager\QueueServiceTaskListener;
use Interop\Container\ContainerInterface;

class SourceProvider extends Listener\AbstractListenerProvider {
    /**
     * Class constructor.
     *
     * @param \Interop\Container\ContainerInterface $container The container
     */
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
            Source\CRA::class => [
                new Listener\LogFiredEventListener($container->get('log')('Event'))
            ],
            Source\OTP::class => [
                new Listener\LogFiredEventListener($container->get('log')('Event')),
                new QueueServiceTaskListener($credentialRepository, $serviceHandlerRepository, $eventFactory, $emitter, $gearmanClient)
            ],
            Source\Created::class => [
                new Listener\LogFiredEventListener($container->get('log')('Event'))
            ],
            Source\Updated::class => [
                new Listener\LogFiredEventListener($container->get('log')('Event'))
            ],
            Source\Deleted::class => [
                new Listener\LogFiredEventListener($container->get('log')('Event'))
            ],
            Source\DeletedMulti::class => [
                new Listener\LogFiredEventListener($container->get('log')('Event'))
            ]
        ];
    }
}
