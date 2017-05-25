<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Provider\Company;

use App\Provider\AbstractProvider;
use App\Event\Company\Invitation;
use App\Listener;
use App\Listener\Manager\QueueServiceTaskListener;
use Interop\Container\ContainerInterface;
use Refinery29\Event\LazyListener;

class InvitationProvider extends AbstractProvider {
    /**
     * Class constructor.
     *
     * @param \Interop\Container\ContainerInterface $container
     *
     * @return void
     */
    public function __construct(ContainerInterface $container) {
        $this->events = [
            Invitation\Created::class => [
                LazyListener::fromAlias(
                    QueueServiceTaskListener::class,
                    $container
                ),
                LazyListener::fromAlias(
                    Listener\LogFiredEventListener::class,
                    $container
                ),
                LazyListener::fromAlias(
                    Listener\MetricEventListener::class,
                    $container
                )
            ],
            Invitation\Resend::class => [
                LazyListener::fromAlias(
                    QueueServiceTaskListener::class,
                    $container
                ),
                LazyListener::fromAlias(
                    Listener\LogFiredEventListener::class,
                    $container
                )
            ],
            Invitation\Updated::class => [
                LazyListener::fromAlias(
                    Listener\LogFiredEventListener::class,
                    $container
                )
            ]
        ];
    }
}
