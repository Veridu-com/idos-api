<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Feature;

use App\Event\AbstractEvent;
use Illuminate\Support\Collection;

/**
 * Deleted event for multiple features.
 */
class DeletedMulti extends AbstractEvent {
    /**
     * Event related Features.
     *
     * @var \Illuminate\Support\Collection
     */
    public $features;

    /**
     * Class constructor.
     *
     * @param \Illuminate\Support\Collection $features
     *
     * @return void
     */
    public function __construct(Collection $features) {
        $this->features = $features;
    }
}
