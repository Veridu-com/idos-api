<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Profile\Candidate;

use App\Entity\Company\Credential;
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
     * Event related Credential.
     *
     * @var \App\Entity\Company\Credential
     */
    public $credential;

    /**
     * Class constructor.
     *
     * @param \App\Entity\Profile\Candidate  $candidate
     * @param \App\Entity\Company\Credential $credential
     *
     * @return void
     */
    public function __construct(Candidate $candidate, Credential $credential) {
        $this->candidate  = $candidate;
        $this->credential = $credential;
    }
}
