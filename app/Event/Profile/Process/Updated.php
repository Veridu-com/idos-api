<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Profile\Process;

use App\Entity\Profile\Process;
use App\Event\AbstractEvent;

/**
 * Updated event.
 */
class Updated extends AbstractEvent {
    /**
     * Event related Process.
     *
     * @var App\Entity\Profile\Process
     */
    public $process;

    /**
     * Class constructor.
     *
     * @param App\Entity\Profile\Process $process
     *
     * @return void
     */
    public function __construct(Process $process) {
        $this->process = $process;
    }
}
