<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Provider\Profile;

use App\Event\Profile\Source\CRA;
use App\Event\Profile\Source\Created;
use App\Event\Profile\Source\Deleted;
use App\Event\Profile\Source\DeletedMulti;
use App\Event\Profile\Source\File;
use App\Event\Profile\Source\OTP;
use App\Event\Profile\Source\Updated;
use App\Listener\EventLogger;
use App\Listener\Manager\ScrapeScheduler;
use App\Listener\Manager\ServiceScheduler;
use App\Listener\MetricGenerator;
use App\Listener\Profile\Source\Logout;
use App\Listener\Profile\Source\SaveFileToStorage;
use App\Provider\AbstractProvider;
use Interop\Container\ContainerInterface;
use Refinery29\Event\LazyListener;

class Source extends AbstractProvider {
    /**
     * Class constructor.
     *
     * @param \Interop\Container\ContainerInterface $container The container
     */
    public function __construct(ContainerInterface $container) {
        $this->events = [
            CRA::class => [
                LazyListener::fromAlias(
                    EventLogger::class,
                    $container
                ),
                LazyListener::fromAlias(
                    MetricGenerator::class,
                    $container
                ),
                LazyListener::fromAlias(
                    ServiceScheduler::class,
                    $container
                )
            ],
            Created::class => [
                LazyListener::fromAlias(
                    EventLogger::class,
                    $container
                ),
                LazyListener::fromAlias(
                    ScrapeScheduler::class,
                    $container
                ),
                LazyListener::fromAlias(
                    MetricGenerator::class,
                    $container
                )
            ],
            Deleted::class => [
                LazyListener::fromAlias(
                    EventLogger::class,
                    $container
                ),
                LazyListener::fromAlias(
                    Logout::class,
                    $container
                ),
                LazyListener::fromAlias(
                    MetricGenerator::class,
                    $container
                )
            ],
            DeletedMulti::class => [
                LazyListener::fromAlias(
                    EventLogger::class,
                    $container
                ),
                LazyListener::fromAlias(
                    Logout::class,
                    $container
                ),
                LazyListener::fromAlias(
                    MetricGenerator::class,
                    $container
                )
            ],
            File::class => [
                LazyListener::fromAlias(
                    EventLogger::class,
                    $container
                ),
                LazyListener::fromAlias(
                    MetricGenerator::class,
                    $container
                ),
                LazyListener::fromAlias(
                    SaveFileToStorage::class,
                    $container
                )
            ],
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
            OTP::class => [
                LazyListener::fromAlias(
                    EventLogger::class,
                    $container
                ),
                LazyListener::fromAlias(
                    MetricGenerator::class,
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
