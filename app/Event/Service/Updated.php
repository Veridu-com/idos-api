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
 * Updated event.
 */
class Updated extends AbstractEvent {
    /**
     * Event related Service.
     *
     * @var \App\Entity\Service
     */
    public $service;

    /**
     * Class constructor.
     *
     * @param \App\Entity\Service $service
     *
     * @return void
     */
    public function __construct(Service $service) {
        $this->service = $service;
    }
}
