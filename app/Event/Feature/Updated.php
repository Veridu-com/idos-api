<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Feature;

use App\Entity\Feature;
use App\Event\AbstractEvent;

/**
 * Updated event.
 */
class Updated extends AbstractEvent {
    /**
     * Event related Feature.
     *
     * @var App\Entity\Feature
     */
    public $feature;

    /**
     * Class constructor.
     *
     * @param App\Entity\Feature $feature
     *
     * @return void
     */
    public function __construct(Feature $feature) {
        $this->feature = $feature;
    }
}
