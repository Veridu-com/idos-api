<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Profile\Warning;

use App\Event\AbstractEvent;
use Illuminate\Support\Collection;

/**
 * Deleted event for multiple warnings.
 */
class DeletedMulti extends AbstractEvent {
    /**
     * Event related Warnings.
     *
     * @var \Illuminate\Support\Collection
     */
    public $warnings;

    /**
     * Class constructor.
     *
     * @param \Illuminate\Support\Collection $warnings
     *
     * @return void
     */
    public function __construct(Collection $warnings) {
        $this->warnings = $warnings;
    }
}
