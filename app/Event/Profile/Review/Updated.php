<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Profile\Review;

use App\Entity\Identity;
use App\Entity\Profile\Review;
use App\Event\AbstractEvent;

/**
 * Updated event.
 */
class Updated extends AbstractEvent {
    /**
     * Event related Review.
     *
     * @var \App\Entity\Profile\Review
     */
    public $review;
    /**
     * Event related Identity.
     *
     * @var \App\Entity\Identity
     */
    public $identity;

    /**
     * Class constructor.
     *
     * @param \App\Entity\Profile\Review $review
     * @param \App\Entity\Identity       $identity
     *
     * @return void
     */
    public function __construct(Review $review, Identity $identity) {
        $this->review   = $review;
        $this->identity = $identity;
    }
}
