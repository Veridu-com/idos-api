<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Raw;

use App\Entity\Raw;
use App\Event\AbstractEvent;

/**
 * Deleted event.
 */
class Deleted extends AbstractEvent {
    /**
     * Event related Raw.
     *
     * @var App\Entity\Raw
     */
    public $raw;

    /**
     * Class constructor.
     *
     * @param App\Entity\Raw $raw
     *
     * @return void
     */
    public function __construct(Raw $raw) {
        $this->raw = $raw;
    }
}
