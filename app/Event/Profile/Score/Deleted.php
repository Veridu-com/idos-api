<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Profile\Score;

use App\Entity\Profile\Score;
use App\Event\AbstractEvent;

/**
 * Deleted event.
 */
class Deleted extends AbstractEvent {
    /**
     * Event related Score.
     *
     * @var \App\Entity\Profile\Score
     */
    public $score;

    /**
     * Class constructor.
     *
     * @param \App\Entity\Profile\Score $score
     *
     * @return void
     */
    public function __construct(Score $score) {
        $this->score = $score;
    }
}
