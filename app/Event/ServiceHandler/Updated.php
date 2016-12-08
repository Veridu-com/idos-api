<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\ServiceHandler;

use App\Entity\Identity;
use App\Entity\ServiceHandler;
use App\Event\AbstractEvent;

/**
 * Updated event.
 */
class Updated extends AbstractEvent {
    /**
     * Event related ServiceHandler.
     *
     * @var \App\Entity\ServiceHandler
     */
    public $serviceHandler;
    /**
     * Event related Identity.
     *
     * @var \App\Entity\Identity
     */
    public $identity;

    /**
     * Class constructor.
     *
     * @param \App\Entity\ServiceHandler $serviceHandler
     * @param \App\Entity\Identity       $identity
     *
     * @return void
     */
    public function __construct(ServiceHandler $serviceHandler, Identity $identity) {
        $this->serviceHandler = $serviceHandler;
        $this->identity       = $identity;
    }
}
