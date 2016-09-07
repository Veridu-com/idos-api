<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Normalised;

use App\Event\AbstractEvent;
use Illuminate\Support\Collection;

/**
 * Deleted event for multiple normalised items.
 */
class DeletedMulti extends AbstractEvent {
    /**
     * Event related Normalised items.
     *
     * @var \Illuminate\Support\Collection
     */
    public $items;

    /**
     * Class constructor.
     *
     * @param \Illuminate\Support\Collection $items
     *
     * @return void
     */
    public function __construct(Collection $items) {
        $this->items = $items;
    }
}
