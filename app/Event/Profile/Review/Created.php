<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Profile\Review;

use App\Entity\Profile\Review;
use App\Event\AbstractEvent;

/**
 * Created event.
 */
class Created extends AbstractEvent {
    /**
     * Event related Review.
     *
     * @var App\Entity\Profile\Review
     */
    public $review;

    /**
     * Class constructor.
     *
     * @param App\Entity\Profile\Review $review
     *
     * @return void
     */
    public function __construct(Review $review) {
        $this->review = $review;
    }
}
