<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Listener\Profile;

use App\Event\Profile\Feature;
use App\Listener;
use Interop\Container\ContainerInterface;

class FeatureProvider extends Listener\AbstractListenerProvider {
    public function __construct(ContainerInterface $container) {
        $this->events = [
            Feature\Created::class => [
                new Listener\LogFiredEventListener($container->get('commandBus'), $container->get('commandFactory'), $container->get('log')('Event'))
            ],
            Feature\Updated::class => [
                new Listener\LogFiredEventListener($container->get('commandBus'), $container->get('commandFactory'), $container->get('log')('Event'))
            ],
            Feature\Deleted::class => [
                new Listener\LogFiredEventListener($container->get('commandBus'), $container->get('commandFactory'), $container->get('log')('Event'))
            ],
            Feature\DeletedMulti::class => [
                new Listener\LogFiredEventListener($container->get('commandBus'), $container->get('commandFactory'), $container->get('log')('Event'))
            ]
        ];
    }
}
