<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Gate;

use App\Entity\Gate;
use App\Event\AbstractEvent;

/**
 * Updated event.
 */
class Updated extends AbstractEvent {
    /**
     * Event related Gate.
     *
     * @var App\Entity\Gate
     */
    public $gate;

    /**
     * Class constructor.
     *
     * @param App\Entity\Gate $gate
     *
     * @return void
     */
    public function __construct(Gate $gate) {
        $this->gate = $gate;
    }
}
