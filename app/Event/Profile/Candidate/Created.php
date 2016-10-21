<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Profile\Candidate;

use App\Entity\Profile\Candidate;
use App\Entity\User;
use App\Event\AbstractEvent;

/**
 * Created event.
 */
class Created extends AbstractEvent {
    /**
     * Event related User.
     *
     * @var \App\Entity\User
     */
    public $user;
    /**
     * Event related Candidate.
     *
     * @var \App\Entity\Profile\Candidate
     */
    public $candidate;

    /**
     * Class constructor.
     *
     * @param \App\Entity\User              $user
     * @param \App\Entity\Profile\Attribute $candidate
     *
     * @return void
     */
    public function __construct(User $user, Candidate $candidate) {
        $this->user      = $user;
        $this->candidate = $candidate;
    }
}
