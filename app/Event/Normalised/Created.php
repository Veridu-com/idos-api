<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Normalised;

use App\Entity\Normalised;
use App\Event\AbstractEvent;

/**
 * Created event.
 */
class Created extends AbstractEvent {
    /**
     * Event related Normalised.
     *
     * @var App\Entity\Normalised
     */
    public $normalised;

    /**
     * Class constructor.
     *
     * @param App\Entity\Normalised $normalised
     *
     * @return void
     */
    public function __construct(Normalised $normalised) {
        $this->normalised = $normalised;
    }
}
