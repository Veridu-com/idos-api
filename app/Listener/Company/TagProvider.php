<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Listener\Company;

use App\Event\Company\Tag;
use App\Listener;
use Interop\Container\ContainerInterface;

class TagProvider extends Listener\AbstractListenerProvider {
    public function __construct(ContainerInterface $container) {
        $this->events = [
            Tag\Created::class => [
                new Listener\LogFiredEventListener($container->get('log')('Event'))
            ],
            Tag\Updated::class => [
                new Listener\LogFiredEventListener($container->get('log')('Event'))
            ],
            Tag\Deleted::class => [
                new Listener\LogFiredEventListener($container->get('log')('Event'))
            ],
            Tag\DeletedMulti::class => [
                new Listener\LogFiredEventListener($container->get('log')('Event'))
            ]
        ];
    }
}
