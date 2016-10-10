<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\ServiceHandler;

use App\Entity\ServiceHandler;
use App\Event\AbstractEvent;

/**
 * Deleted event.
 */
class Deleted extends AbstractEvent {
    /**
     * Event related ServiceHandler.
     *
     * @var \App\Entity\ServiceHandler
     */
    public $serviceHandler;

    /**
     * Class constructor.
     *
     * @param \App\Entity\ServiceHandler $serviceHandler
     *
     * @return void
     */
    public function __construct(ServiceHandler $serviceHandler) {
        $this->serviceHandler = $serviceHandler;
    }
}
