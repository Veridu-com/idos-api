<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Provider\Profile;

use App\Provider\AbstractProvider;
use App\Event\Profile\Gate;
use App\Listener;
use App\Listener\Manager\QueueServiceTaskListener;
use App\Listener\Profile\Recommendation\EvaluateRecommendationListener;
use Interop\Container\ContainerInterface;
use Refinery29\Event\LazyListener;

class GateProvider extends AbstractProvider {
    /**
     * Class constructor.
     *
     * @param \Interop\Container\ContainerInterface $container
     *
     * @return void
     */
    public function __construct(ContainerInterface $container) {
        $this->events = [
            Gate\Created::class => [
                LazyListener::fromAlias(
                    Listener\LogFiredEventListener::class,
                    $container
                ),
                LazyListener::fromAlias(
                    EvaluateRecommendationListener::class,
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
            Gate\Updated::class => [
                LazyListener::fromAlias(
                    Listener\LogFiredEventListener::class,
                    $container
                ),
                LazyListener::fromAlias(
                    EvaluateRecommendationListener::class,
                    $container
                ),
                LazyListener::fromAlias(
                    Listener\MetricEventListener::class,
                    $container
                )
            ],
            Gate\Deleted::class => [
                LazyListener::fromAlias(
                    Listener\LogFiredEventListener::class,
                    $container
                ),
                LazyListener::fromAlias(
                    EvaluateRecommendationListener::class,
                    $container
                ),
                LazyListener::fromAlias(
                    Listener\MetricEventListener::class,
                    $container
                )
            ],
            Gate\DeletedMulti::class => [
                LazyListener::fromAlias(
                    Listener\LogFiredEventListener::class,
                    $container
                ),
                LazyListener::fromAlias(
                    EvaluateRecommendationListener::class,
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
