<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Listener\Company;

use App\Event\Company\Credential;
use App\Listener;
use Interop\Container\ContainerInterface;

class CredentialProvider extends Listener\AbstractListenerProvider {
    public function __construct(ContainerInterface $container) {
        $this->events = [
            Credential\Created::class => [
                new Listener\LogFiredEventListener($container->get('commandBus'), $container->get('commandFactory'), $container->get('log')('Event'))
            ],
            Credential\Updated::class => [
                new Listener\LogFiredEventListener($container->get('commandBus'), $container->get('commandFactory'), $container->get('log')('Event'))
            ],
            Credential\Deleted::class => [
                new Listener\LogFiredEventListener($container->get('commandBus'), $container->get('commandFactory'), $container->get('log')('Event'))
            ]
        ];
    }
}
