<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Profile\Raw;

use App\Entity\Profile\Raw;
use App\Event\AbstractEvent;

/**
 * Updated event.
 */
class Updated extends AbstractEvent {
    /**
     * Event related Raw.
     *
     * @var App\Entity\Profile\Raw
     */
    public $raw;

    /**
     * Class constructor.
     *
     * @param App\Entity\Profile\Raw $raw
     *
     * @return void
     */
    public function __construct(Raw $raw) {
        $this->raw = $raw;
    }
}
