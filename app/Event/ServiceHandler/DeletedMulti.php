<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\ServiceHandler;

use App\Event\AbstractEvent;
use Illuminate\Support\Collection;

/**
 * Deleted event for multiple service handlers.
 */
class DeletedMulti extends AbstractEvent {
    /**
     * Event related ServiceHandlers.
     *
     * @var \Illuminate\Support\Collection
     */
    public $serviceHandlers;

    /**
     * Class constructor.
     *
     * @param \Illuminate\Support\Collection $serviceHandlers
     *
     * @return void
     */
    public function __construct(Collection $serviceHandlers) {
        $this->serviceHandlers = $serviceHandlers;
    }
}
