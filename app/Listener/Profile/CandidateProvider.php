<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Listener\Profile;

use App\Event\Candidate;
use App\Listener;
use Interop\Container\ContainerInterface;

class CandidateProvider extends Listener\AbstractListenerProvider {
    public function __construct(ContainerInterface $container) {
        $this->events = [
            Candidate\Created::class => [
                new Listener\LogFiredEventListener($container->get('log')('Event'))
            ],
            Candidate\Updated::class => [
                new Listener\LogFiredEventListener($container->get('log')('Event'))
            ],
            Candidate\Deleted::class => [
                new Listener\LogFiredEventListener($container->get('log')('Event'))
            ],
            Candidate\DeletedMulti::class => [
                new Listener\LogFiredEventListener($container->get('log')('Event'))
            ]
        ];
    }
}
