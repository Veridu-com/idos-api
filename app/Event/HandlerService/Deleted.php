<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\HandlerService;

use App\Entity\Identity;
use App\Entity\HandlerService;
use App\Event\AbstractEvent;

/**
 * Deleted event.
 */
class Deleted extends AbstractEvent {
    /**
     * Event related HandlerService.
     *
     * @var \App\Entity\HandlerService
     */
    public $serviceHandlerService;
    /**
     * Event related Identity.
     *
     * @var \App\Entity\Identity
     */
    public $identity;

    /**
     * Class constructor.
     *
     * @param \App\Entity\HandlerService $serviceHandlerService
     * @param \App\Entity\Identity       $identity
     *
     * @return void
     */
    public function __construct(HandlerService $serviceHandlerService, Identity $identity) {
        $this->serviceHandlerService = $serviceHandlerService;
        $this->identity       = $identity;
    }
}
