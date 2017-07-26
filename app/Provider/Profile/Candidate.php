<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Provider\Profile;

use App\Event\Profile\Candidate\Created;
use App\Event\Profile\Candidate\Deleted;
use App\Event\Profile\Candidate\DeletedMulti;
use App\Event\Profile\Candidate\Updated;
use App\Listener\EventLogger;
use App\Listener\MetricGenerator;
use App\Listener\Profile\Attribute;
use App\Provider\AbstractProvider;
use Interop\Container\ContainerInterface;
use Refinery29\Event\LazyListener;

class Candidate extends AbstractProvider {
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
                ),
                LazyListener::fromAlias(
                    Attribute::class,
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
                ),
                LazyListener::fromAlias(
                    Attribute::class,
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
                ),
                LazyListener::fromAlias(
                    Attribute::class,
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
                ),
                LazyListener::fromAlias(
                    Attribute::class,
                    $container
                )
            ]
        ];
    }
}
