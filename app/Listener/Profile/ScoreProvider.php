<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Listener\Profile;

use App\Event\Score;
use App\Listener;
use Interop\Container\ContainerInterface;

class ScoreProvider extends Listener\AbstractListenerProvider {
    public function __construct(ContainerInterface $container) {
        $this->events = [
            Score\Created::class => [
                new Listener\LogFiredEventListener($container->get('log')('Event'))
            ],
            Score\Updated::class => [
                new Listener\LogFiredEventListener($container->get('log')('Event'))
            ],
            Score\Deleted::class => [
                new Listener\LogFiredEventListener($container->get('log')('Event'))
            ],
            Score\DeletedMulti::class => [
                new Listener\LogFiredEventListener($container->get('log')('Event'))
            ]
        ];
    }
}
