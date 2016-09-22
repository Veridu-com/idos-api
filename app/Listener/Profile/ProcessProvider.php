<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Listener\Profile;

use App\Event\Process;
use App\Listener;
use Interop\Container\ContainerInterface;

class ProcessProvider extends Listener\AbstractListenerProvider {
    public function __construct(ContainerInterface $container) {
        $this->events = [
            Process\Created::class => [
                new Listener\LogFiredEventListener($container->get('log')('Event'))
            ],
            Process\Updated::class => [
                new Listener\LogFiredEventListener($container->get('log')('Event'))
            ]
        ];
    }
}
