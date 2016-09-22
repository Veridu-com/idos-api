<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Listener\Source;

use App\Event\Profile\Source;
use App\Listener;
use Interop\Container\ContainerInterface;

class ListenerProvider extends Listener\AbstractListenerProvider {
    public function __construct(ContainerInterface $container) {
        $this->events = [
            Source\Created::class => [
                new Listener\LogFiredEventListener($container->get('log')('API'))
            ],
            Source\Updated::class => [
                new Listener\LogFiredEventListener($container->get('log')('API'))
            ],
            Source\Deleted::class => [
                new Listener\LogFiredEventListener($container->get('log')('API'))
            ],
            Source\DeletedMulti::class => [
                new Listener\LogFiredEventListener($container->get('log')('API'))
            ]
        ];
    }
}
