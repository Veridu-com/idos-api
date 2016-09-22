<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Listener\Profile;

use App\Event\Warning;
use App\Listener;
use Interop\Container\ContainerInterface;

class WarningProvider extends Listener\AbstractListenerProvider {
    public function __construct(ContainerInterface $container) {
        $this->events = [
            Warning\Created::class => [
                new Listener\LogFiredEventListener($container->get('log')('Event'))
            ],
            Warning\Deleted::class => [
                new Listener\LogFiredEventListener($container->get('log')('Event'))
            ],
            Warning\DeletedMulti::class => [
                new Listener\LogFiredEventListener($container->get('log')('Event'))
            ]
        ];
    }
}
