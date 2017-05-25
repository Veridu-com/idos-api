<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Provider\Profile;

use App\Event\Profile\Flag;
use App\Provider\AbstractProvider;
use App\Listener;
use App\Listener\Profile\Recommendation\EvaluateRecommendationListener;
use Interop\Container\ContainerInterface;
use Refinery29\Event\LazyListener;

class FlagProvider extends AbstractProvider {
    /**
     * Class constructor.
     *
     * @param \Interop\Container\ContainerInterface $container
     *
     * @return void
     */
    public function __construct(ContainerInterface $container) {
        $this->events = [
            Flag\Created::class => [
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
            Flag\Deleted::class => [
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
            Flag\DeletedMulti::class => [
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
