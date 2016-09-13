<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Process;

use App\Entity\Process;
use App\Event\AbstractEvent;

/**
 * Created event.
 */
class Created extends AbstractEvent {
    /**
     * Event related Process.
     *
     * @var App\Entity\Process
     */
    public $process;

    /**
     * Class constructor.
     *
     * @param App\Entity\Process $process
     *
     * @return void
     */
    public function __construct(Process $process) {
        $this->process = $process;
    }
}
