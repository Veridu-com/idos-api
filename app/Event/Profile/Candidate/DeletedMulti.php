<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Profile\Candidate;

use App\Event\AbstractEvent;
use Illuminate\Support\Collection;

/**
 * Deleted event for multiple candidates.
 */
class DeletedMulti extends AbstractEvent {
    /**
     * Event related Candidates.
     *
     * @var \Illuminate\Support\Collection
     */
    public $candidates;

    /**
     * Class constructor.
     *
     * @param \Illuminate\Support\Collection $candidates
     *
     * @return void
     */
    public function __construct(Collection $candidates) {
        $this->candidates = $candidates;
    }
}
