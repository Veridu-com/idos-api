<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Digested;

use App\Entity\Digested;
use App\Event\AbstractEvent;

/**
 * Updated event.
 */
class Updated extends AbstractEvent {
    /**
     * Event related Digested.
     *
     * @var App\Entity\Digested
     */
    public $digested;

    /**
     * Class constructor.
     *
     * @param App\Entity\Digested $digested
     *
     * @return void
     */
    public function __construct(Digested $digested) {
        $this->digested = $digested;
    }
}
