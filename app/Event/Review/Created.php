<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Review;

use App\Entity\Review;
use App\Event\AbstractEvent;

/**
 * Created event.
 */
class Created extends AbstractEvent {
    /**
     * Event related Review.
     *
     * @var App\Entity\Review
     */
    public $review;

    /**
     * Class constructor.
     *
     * @param App\Entity\Review $review
     *
     * @return void
     */
    public function __construct(Review $review) {
        $this->review = $review;
    }
}
