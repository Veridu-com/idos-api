<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Provider\Profile;

use App\Event\Profile\Attribute\Created;
use App\Event\Profile\Attribute\Deleted;
use App\Event\Profile\Attribute\DeletedMulti;
use App\Event\Profile\Attribute\Updated;
use App\Event\Profile\Attribute\UpsertedBulk;
use App\Listener\EventLogger;
use App\Listener\MetricGenerator;
use App\Listener\Profile\Recommendation\EvaluateRecommendation;
use App\Provider\AbstractProvider;
use Interop\Container\ContainerInterface;
use Refinery29\Event\LazyListener;

class Attribute extends AbstractProvider {
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
                    EvaluateRecommendation::class,
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
                    EvaluateRecommendation::class,
                    $container
                ),
                LazyListener::fromAlias(
                    MetricGenerator::class,
                    $container
                )
            ],
            UpsertedBulk::class => [
                LazyListener::fromAlias(
                    EventLogger::class,
                    $container
                ),
                LazyListener::fromAlias(
                    EvaluateRecommendation::class,
                    $container
                ),
                // @FIXME talk with team about it.
                // new MetricGenerator($commandBus, $commandFactory)
            ],
            Deleted::class => [
                LazyListener::fromAlias(
                    EventLogger::class,
                    $container
                ),
                LazyListener::fromAlias(
                    EvaluateRecommendation::class,
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
                    EvaluateRecommendation::class,
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
