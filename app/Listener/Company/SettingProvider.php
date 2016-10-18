<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Listener\Company;

use App\Event\Company\Setting;
use App\Listener;
use Interop\Container\ContainerInterface;

class SettingProvider extends Listener\AbstractListenerProvider {
    public function __construct(ContainerInterface $container) {
        $this->events = [
            Setting\Created::class => [
                new Listener\LogFiredEventListener($container->get('log')('Event'))
            ],
            Setting\Updated::class => [
                new Listener\LogFiredEventListener($container->get('log')('Event'))
            ],
            Setting\Deleted::class => [
                new Listener\LogFiredEventListener($container->get('log')('Event'))
            ],
            Setting\DeletedMulti::class => [
                new Listener\LogFiredEventListener($container->get('log')('Event'))
            ]
        ];
    }
}
