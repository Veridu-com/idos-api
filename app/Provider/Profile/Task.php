<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Provider\Profile;

use App\Event\Profile\Task\Completed;
use App\Event\Profile\Task\Created;
use App\Event\Profile\Task\Updated;
use App\Listener\EventLogger;
use App\Listener\Manager\ServiceScheduler;
use App\Listener\MetricGenerator;
use App\Provider\AbstractProvider;
use Interop\Container\ContainerInterface;
use Refinery29\Event\LazyListener;

class Task extends AbstractProvider {
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
                    EventLogger::class,
                    $container
                ),
                LazyListener::fromAlias(
                    MetricGenerator::class,
                    $container
                )
            ],
            // @FIXME Talk to Flavio if we need this granularity
            // task [
            //  'onUpdated': [ event1, event2]
            //  'onCompleted': [ event3, event4]
            // ]
            // Uses: Scraper calling other servies.
            Updated::class => [
                LazyListener::fromAlias(
                    EventLogger::class,
                    $container
                ),
                LazyListener::fromAlias(
                    MetricGenerator::class,
                    $container
                )
            ],
            Completed::class => [
                LazyListener::fromAlias(
                    EventLogger::class,
                    $container
                ),
                LazyListener::fromAlias(
                    ServiceScheduler::class,
                    $container
                )
            ]
        ];
    }
}
