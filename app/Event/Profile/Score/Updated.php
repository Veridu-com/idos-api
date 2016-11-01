<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Profile\Score;

use App\Entity\Profile\Score;
use App\Entity\Company\Credential;
use App\Event\AbstractEvent;

/**
 * Updated event.
 */
class Updated extends AbstractEvent {
    /**
     * Event related Score.
     *
     * @var \App\Entity\Profile\Score
     */
    public $score;
    /**
     * Event related Credential.
     *
     * @var \App\Entity\Company\Credential
     */
    public $credential;

    /**
     * Class constructor.
     *
     * @param \App\Entity\Profile\Score $score
     *
     * @return void
     */
    public function __construct(Score $score, Credential $credential) {
        $this->score = $score;
        $this->credential = $credential;
    }
}
