<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Profile\Review;

use App\Entity\Profile\Review;
use App\Entity\Company\Credential;
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
     * Event related Credential.
     *
     * @var \App\Entity\Company\Credential
     */
    public $actor;

    /**
     * Class constructor.
     *
     * @param \App\Entity\Profile\Review $review
     *
     * @return void
     */
    public function __construct(Review $review, Credential $actor) {
        $this->review = $review;
        $this->actor = $actor;
    }
}
