<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\HandlerService;

use App\Entity\Identity;
use App\Event\AbstractEvent;
use Illuminate\Support\Collection;

/**
 * Deleted event for multiple service handlers.
 */
class DeletedMulti extends AbstractEvent {
    /**
     * Event related HandlerServices.
     *
     * @var \Illuminate\Support\Collection
     */
    public $serviceHandlerServices;
    /**
     * Event related Identity.
     *
     * @var \App\Entity\Identity
     */
    public $identity;

    /**
     * Class constructor.
     *
     * @param \Illuminate\Support\Collection $serviceHandlerServices
     * @param \App\Entity\Identity           $identity
     *
     * @return void
     */
    public function __construct(Collection $serviceHandlerServices, Identity $identity) {
        $this->serviceHandlerServices = $serviceHandlerServices;
        $this->identity        = $identity;
    }
}
