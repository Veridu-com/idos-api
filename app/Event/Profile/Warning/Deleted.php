<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Profile\Warning;

use App\Entity\Profile\Warning;
use App\Event\AbstractEvent;

/**
 * Deleted event.
 */
class Deleted extends AbstractEvent {
    /**
     * Event related Warning.
     *
     * @var App\Entity\Profile\Warning
     */
    public $warning;

    /**
     * Class constructor.
     *
     * @param App\Entity\Profile\Warning $warning
     *
     * @return void
     */
    public function __construct(Warning $warning) {
        $this->warning = $warning;
    }
}
