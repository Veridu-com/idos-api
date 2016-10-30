<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Listener\Profile;

use App\Event\Profile\Source;
use App\Listener;
use Interop\Container\ContainerInterface;

class SourceProvider extends Listener\AbstractListenerProvider {
    public function __construct(ContainerInterface $container) {
        $this->events = [
            Source\CRA::class => [
                new Listener\LogFiredEventListener($container->get('commandBus'), $container->get('commandFactory'), $container->get('log')('Event'))
            ],
            Source\OTP::class => [
                new Listener\LogFiredEventListener($container->get('commandBus'), $container->get('commandFactory'), $container->get('log')('Event'))
            ],
            Source\Created::class => [
                new Listener\LogFiredEventListener($container->get('commandBus'), $container->get('commandFactory'), $container->get('log')('Event'))
            ],
            Source\Updated::class => [
                new Listener\LogFiredEventListener($container->get('commandBus'), $container->get('commandFactory'), $container->get('log')('Event'))
            ],
            Source\Deleted::class => [
                new Listener\LogFiredEventListener($container->get('commandBus'), $container->get('commandFactory'), $container->get('log')('Event'))
            ],
            Source\DeletedMulti::class => [
                new Listener\LogFiredEventListener($container->get('commandBus'), $container->get('commandFactory'), $container->get('log')('Event'))
            ]
        ];
    }
}
