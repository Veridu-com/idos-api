<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Digested;

use App\Event\AbstractEvent;
use Illuminate\Support\Collection;

/**
 * Deleted event for multiple digested items.
 */
class DeletedMulti extends AbstractEvent {
    /**
     * Event related Digested items.
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
