<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Provider\Profile;

use App\Event\Profile\Feature;
use App\Provider\AbstractProvider;
use App\Listener;
use App\Listener\Manager\QueueServiceTaskListener;
use Interop\Container\ContainerInterface;
use Refinery29\Event\LazyListener;

class FeatureProvider extends AbstractProvider {
    /**
     * Class constructor.
     *
     * @param \Interop\Container\ContainerInterface $container
     *
     * @return void
     */
    public function __construct(ContainerInterface $container) {
        $this->events = [
            Feature\CreatedBulk::class => [
                LazyListener::fromAlias(
                    Listener\LogFiredEventListener::class,
                    $container
                ),
                LazyListener::fromAlias(
                    Listener\Profile\Source\AddSourceTagFromUpsertBulkFeatureListener::class,
                    $container
                ),
                LazyListener::fromAlias(
                    QueueServiceTaskListener::class,
                    $container
                )
            ],
            Feature\Created::class => [
                LazyListener::fromAlias(
                    Listener\LogFiredEventListener::class,
                    $container
                ),
                LazyListener::fromAlias(
                    Listener\Profile\Source\AddSourceTagFromCreateFeatureListener::class,
                    $container
                ),
                LazyListener::fromAlias(
                    QueueServiceTaskListener::class,
                    $container
                ),
                LazyListener::fromAlias(
                    Listener\MetricEventListener::class,
                    $container
                )
            ],
            Feature\Updated::class => [
                LazyListener::fromAlias(
                    Listener\LogFiredEventListener::class,
                    $container
                ),
                LazyListener::fromAlias(
                    QueueServiceTaskListener::class,
                    $container
                ),
                LazyListener::fromAlias(
                    Listener\MetricEventListener::class,
                    $container
                )
            ],
            Feature\Deleted::class => [
                LazyListener::fromAlias(
                    Listener\LogFiredEventListener::class,
                    $container
                ),
                LazyListener::fromAlias(
                    Listener\MetricEventListener::class,
                    $container
                )
            ],
            Feature\DeletedMulti::class => [
                LazyListener::fromAlias(
                    Listener\LogFiredEventListener::class,
                    $container
                ),
                LazyListener::fromAlias(
                    Listener\MetricEventListener::class,
                    $container
                )
            ]
        ];
    }
}
