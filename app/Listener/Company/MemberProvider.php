<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Listener\Company;

use App\Event\Member;
use App\Listener;
use Interop\Container\ContainerInterface;

class MemberProvider extends Listener\AbstractListenerProvider {
    public function __construct(ContainerInterface $container) {
        $this->events = [
            Member\Created::class => [
                new Listener\LogFiredEventListener($container->get('log')('Event'))
            ],
            Member\Deleted::class => [
                new Listener\LogFiredEventListener($container->get('log')('Event'))
            ]
        ];
    }
}
