<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Profile\Feature;

use App\Entity\Profile\Feature;
use App\Event\AbstractEvent;

/**
 * Deleted event.
 */
class Deleted extends AbstractEvent {
    /**
     * Event related Feature.
     *
     * @var App\Entity\Profile\Feature
     */
    public $feature;

    /**
     * Class constructor.
     *
     * @param App\Entity\Profile\Feature $feature
     *
     * @return void
     */
    public function __construct(Feature $feature) {
        $this->feature = $feature;
    }
}
