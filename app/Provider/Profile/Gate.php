<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Provider\Profile;

use App\Event\Profile\Gate\Created;
use App\Event\Profile\Gate\Deleted;
use App\Event\Profile\Gate\DeletedMulti;
use App\Event\Profile\Gate\Updated;
use App\Listener\EventLogger;
use App\Listener\Manager\ServiceScheduler;
use App\Listener\MetricGenerator;
use App\Listener\Profile\Recommendation\EvaluateRecommendation;
use App\Provider\AbstractProvider;
use Interop\Container\ContainerInterface;
use Refinery29\Event\LazyListener;

class Gate extends AbstractProvider {
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
                    EvaluateRecommendation::class,
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
