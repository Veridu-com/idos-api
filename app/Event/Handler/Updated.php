<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Handler;

use App\Entity\Handler;
use App\Entity\Identity;
use App\Event\AbstractEvent;

/**
 * Updated event.
 */
class Updated extends AbstractEvent {
    /**
     * Event related Handler.
     *
     * @var \App\Entity\Handler
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
     * @param \App\Entity\Handler  $serviceHandler
     * @param \App\Entity\Identity $identity
     *
     * @return void
     */
    public function __construct(Handler $serviceHandler, Identity $identity) {
        $this->serviceHandler = $serviceHandler;
        $this->identity       = $identity;
    }
}
