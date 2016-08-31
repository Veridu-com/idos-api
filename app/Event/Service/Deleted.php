<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Service;

use App\Entity\Service;
use App\Event\AbstractEvent;

/**
 * Deleted event.
 */
class Deleted extends AbstractEvent {
    /**
     * Event related Service.
     *
     * @var App\Entity\Service
     */
    public $services;

    /**
     * Class constructor.
     *
     * @param App\Entity\Service $services
     *
     * @return void
     */
    public function __construct(Service $services) {
        $this->services = $services;
    }
}
