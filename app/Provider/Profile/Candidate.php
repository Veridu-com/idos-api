<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Provider\Profile;

use App\Provider\AbstractProvider;
use App\Event\Profile\Candidate;
use App\Listener;
use Interop\Container\ContainerInterface;
use Refinery29\Event\LazyListener;

class CandidateProvider extends AbstractProvider {
    /**
     * Class constructor.
     *
     * @param \Interop\Container\ContainerInterface $container
     *
     * @return void
     */
    public function __construct(ContainerInterface $container) {
        $this->events = [
            Candidate\Created::class => [
                LazyListener::fromAlias(
                    Listener\LogFiredEventListener::class,
                    $container
                ),
                LazyListener::fromAlias(
                    Listener\MetricEventListener::class,
                    $container
                ),
                LazyListener::fromAlias(
                    Listener\Profile\AttributeListener::class,
                    $container
                )
            ],
            Candidate\Updated::class => [
                LazyListener::fromAlias(
                    Listener\LogFiredEventListener::class,
                    $container
                ),
                LazyListener::fromAlias(
                    Listener\MetricEventListener::class,
                    $container
                ),
                LazyListener::fromAlias(
                    Listener\Profile\AttributeListener::class,
                    $container
                )
            ],
            Candidate\Deleted::class => [
                LazyListener::fromAlias(
                    Listener\LogFiredEventListener::class,
                    $container
                ),
                LazyListener::fromAlias(
                    Listener\MetricEventListener::class,
                    $container
                ),
                LazyListener::fromAlias(
                    Listener\Profile\AttributeListener::class,
                    $container
                )
            ],
            Candidate\DeletedMulti::class => [
                LazyListener::fromAlias(
                    Listener\LogFiredEventListener::class,
                    $container
                ),
                LazyListener::fromAlias(
                    Listener\MetricEventListener::class,
                    $container
                ),
                LazyListener::fromAlias(
                    Listener\Profile\AttributeListener::class,
                    $container
                )
            ]
        ];
    }
}
