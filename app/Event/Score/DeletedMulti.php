<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Score;

use App\Event\AbstractEvent;
use Illuminate\Support\Collection;

/**
 * Deleted event for multiple scores.
 */
class DeletedMulti extends AbstractEvent {
    /**
     * Event related Scores.
     *
     * @var \Illuminate\Support\Collection
     */
    public $scores;

    /**
     * Class constructor.
     *
     * @param \Illuminate\Support\Collection $scores
     *
     * @return void
     */
    public function __construct(Collection $scores) {
        $this->scores = $scores;
    }
}
