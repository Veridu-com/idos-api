<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Profile\Flag;

use App\Event\AbstractEvent;
use Illuminate\Support\Collection;

/**
 * Deleted event for multiple flags.
 */
class DeletedMulti extends AbstractEvent {
    /**
     * Event related Flags.
     *
     * @var \Illuminate\Support\Collection
     */
    public $flags;

    /**
     * Class constructor.
     *
     * @param \Illuminate\Support\Collection $flags
     *
     * @return void
     */
    public function __construct(Collection $flags) {
        $this->flags = $flags;
    }
}
