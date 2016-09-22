<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Profile\Gate;

use App\Event\AbstractEvent;
use Illuminate\Support\Collection;

/**
 * Deleted event for multiple gates.
 */
class DeletedMulti extends AbstractEvent {
    /**
     * Event related Gates.
     *
     * @var \Illuminate\Support\Collection
     */
    public $gates;

    /**
     * Class constructor.
     *
     * @param \Illuminate\Support\Collection $gates
     *
     * @return void
     */
    public function __construct(Collection $gates) {
        $this->gates = $gates;
    }
}
