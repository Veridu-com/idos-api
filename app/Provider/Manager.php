<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Provider\Manager;

use App\Event\Manager;
use App\Listener;
use App\Provider\AbstractProvider;
use Interop\Container\ContainerInterface;
use Refinery29\Event\LazyListener;

class ManagerProvider extends AbstractProvider {
    /**
     * Class constructor.
     *
     * @param \Interop\Container\ContainerInterface $container
     *
     * @return void
     */
    public function __construct(ContainerInterface $container) {
        $this->events = [
            Manager\UnhandledEvent::class => [
                LazyListener::fromAlias(
                    Listener\LogFiredEventListener::class,
                    $container
                )
            ],

            Manager\WorkQueued::class => [
                LazyListener::fromAlias(
                    Listener\LogFiredEventListener::class,
                    $container
                )
            ]
        ];
    }
}
