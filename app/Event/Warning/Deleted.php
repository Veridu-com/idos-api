<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Warning;

use App\Entity\Warning;
use App\Event\AbstractEvent;

/**
 * Deleted event.
 */
class Deleted extends AbstractEvent {
    /**
     * Event related Warning.
     *
     * @var App\Entity\Warning
     */
    public $warning;

    /**
     * Class constructor.
     *
     * @param App\Entity\Warning $warning
     *
     * @return void
     */
    public function __construct(Warning $warning) {
        $this->warning = $warning;
    }
}
