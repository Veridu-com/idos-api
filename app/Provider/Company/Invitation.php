<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Provider\Company;

use App\Event\Company\Invitation\Created;
use App\Event\Company\Invitation\Resend;
use App\Event\Company\Invitation\Updated;
use App\Listener\EventLogger;
use App\Listener\Manager\ServiceScheduler;
use App\Listener\MetricGenerator;
use App\Provider\AbstractProvider;
use Interop\Container\ContainerInterface;
use Refinery29\Event\LazyListener;

class Invitation extends AbstractProvider {
    /**
     * Class constructor.
     *
     * @param \Interop\Container\ContainerInterface $container
     *
     * @return void
     */
    public function __construct(ContainerInterface $container) {
        $this->events = [
            Created::class => [
                LazyListener::fromAlias(
                    ServiceScheduler::class,
                    $container
                ),
                LazyListener::fromAlias(
                    EventLogger::class,
                    $container
                ),
                LazyListener::fromAlias(
                    MetricGenerator::class,
                    $container
                )
            ],
            Resend::class => [
                LazyListener::fromAlias(
                    ServiceScheduler::class,
                    $container
                ),
                LazyListener::fromAlias(
                    EventLogger::class,
                    $container
                )
            ],
            Updated::class => [
                LazyListener::fromAlias(
                    EventLogger::class,
                    $container
                )
            ]
        ];
    }
}
