<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Service;

use App\Event\AbstractEvent;
use Illuminate\Support\Collection;

/**
 * Deleted event for multiple services.
 */
class DeletedMulti extends AbstractEvent {
    /**
     * Event related Services.
     *
     * @var \Illuminate\Support\Collection
     */
    public $services;

    /**
     * Class constructor.
     *
     * @param \Illuminate\Support\Collection $services
     *
     * @return void
     */
    public function __construct(Collection $services) {
        $this->services = $services;
    }
}
