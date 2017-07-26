<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Provider\Profile;

use App\Event\Profile\Feature\Created;
use App\Event\Profile\Feature\CreatedBulk;
use App\Event\Profile\Feature\Deleted;
use App\Event\Profile\Feature\DeletedMulti;
use App\Event\Profile\Feature\Updated;
use App\Listener\EventLogger;
use App\Listener\Manager\ServiceScheduler;
use App\Listener\MetricGenerator;
use App\Listener\Profile\Source\AddSourceTagFromCreateFeature;
use App\Listener\Profile\Source\AddSourceTagFromUpsertBulkFeature;
use App\Provider\AbstractProvider;
use Interop\Container\ContainerInterface;
use Refinery29\Event\LazyListener;

class Feature extends AbstractProvider {
    /**
     * Class constructor.
     *
     * @param \Interop\Container\ContainerInterface $container
     *
     * @return void
     */
    public function __construct(ContainerInterface $container) {
        $this->events = [
            CreatedBulk::class => [
                LazyListener::fromAlias(
                    EventLogger::class,
                    $container
                ),
                LazyListener::fromAlias(
                    AddSourceTagFromUpsertBulkFeature::class,
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
                    AddSourceTagFromCreateFeature::class,
                    $container
                ),
                LazyListener::fromAlias(
                    ServiceScheduler::class,
                    $container
                ),
                LazyListener::fromAlias(
                    MetricGenerator::class,
                    $container
                )
            ],
            Updated::class => [
                LazyListener::fromAlias(
                    EventLogger::class,
                    $container
                ),
                LazyListener::fromAlias(
                    ServiceScheduler::class,
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
                    MetricGenerator::class,
                    $container
                )
            ]
        ];
    }
}
