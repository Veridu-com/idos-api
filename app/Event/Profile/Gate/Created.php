<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Profile\Gate;

use App\Entity\Profile\Gate;
use App\Event\AbstractEvent;

/**
 * Created event.
 */
class Created extends AbstractEvent {
    /**
     * Event related Gate.
     *
     * @var App\Entity\Profile\Gate
     */
    public $gate;

    /**
     * Class constructor.
     *
     * @param App\Entity\Profile\Gate $gate
     *
     * @return void
     */
    public function __construct(Gate $gate) {
        $this->gate = $gate;
    }
}
