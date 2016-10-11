<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Profile\Candidate;

use App\Entity\Profile\Candidate;
use App\Event\AbstractEvent;

/**
 * Updated event.
 */
class Updated extends AbstractEvent {
    /**
     * Event related Candidate.
     *
     * @var \App\Entity\Profile\Candidate
     */
    public $candidate;

    /**
     * Class constructor.
     *
     * @param \App\Entity\Profile\Candidate $candidate
     *
     * @return void
     */
    public function __construct(Candidate $candidate) {
        $this->candidate = $candidate;
    }
}
