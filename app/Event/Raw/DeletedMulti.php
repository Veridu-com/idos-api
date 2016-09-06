<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Raw;

use App\Event\AbstractEvent;
use Illuminate\Support\Collection;

/**
 * Deleted event for multiple raw data.
 */
class DeletedMulti extends AbstractEvent {
    /**
     * Event related Raw.
     *
     * @var \Illuminate\Support\Collection
     */
    public $raw;

    /**
     * Class constructor.
     *
     * @param \Illuminate\Support\Collection $raws
     *
     * @return void
     */
    public function __construct(Collection $raw) {
        $this->raw = $raw;
    }
}
