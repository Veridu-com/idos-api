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
 * Created event.
 */
class Created extends AbstractEvent {
    /**
     * Event related Candidate.
     *
     * @var App\Entity\Profile\Candidate
     */
    public $candidate;

    /**
     * Class constructor.
     *
     * @param App\Entity\Profile\Attribute $candidate
     *
     * @return void
     */
    public function __construct(Candidate $candidate) {
        $this->candidate = $candidate;
    }
}
