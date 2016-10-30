<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Service;

use App\Entity\Service;
use App\Entity\Identity;
use App\Event\AbstractEvent;

/**
 * Created event.
 */
class Created extends AbstractEvent {
    /**
     * Event related Service.
     *
     * @var \App\Entity\Service
     */
    public $service;
    /**
     * Event related Identity.
     *
     * @var \App\Entity\Identity
     */
    public $actor;

    /**
     * Class constructor.
     *
     * @param \App\Entity\Service $service
     *
     * @return void
     */
    public function __construct(Service $service, Identity $actor) {
        $this->service = $service;
        $this->actor = $actor;
    }
}
