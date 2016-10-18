<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Listener\Profile;

use App\Event\Profile\Raw;
use App\Listener;
use Interop\Container\ContainerInterface;

class RawProvider extends Listener\AbstractListenerProvider {
    public function __construct(ContainerInterface $container) {
        $this->events = [
            Raw\Created::class => [
                new Listener\LogFiredEventListener($container->get('log')('Event'))
            ],
            Raw\Updated::class => [
                new Listener\LogFiredEventListener($container->get('log')('Event'))
            ],
            Raw\Deleted::class => [
                new Listener\LogFiredEventListener($container->get('log')('Event'))
            ],
            Raw\DeletedMulti::class => [
                new Listener\LogFiredEventListener($container->get('log')('Event'))
            ]
        ];
    }
}
