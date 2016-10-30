<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Profile\Process;

use App\Entity\Profile\Process;
use App\Entity\Company\Credential;
use App\Event\AbstractEvent;

/**
 * Created event.
 */
class Created extends AbstractEvent {
    /**
     * Event related Process.
     *
     * @var \App\Entity\Profile\Process
     */
    public $process;
    /**
     * Event related Credential.
     *
     * @var \App\Entity\Company\Credential
     */
    public $actor;

    /**
     * Class constructor.
     *
     * @param \App\Entity\Profile\Process $process
     *
     * @return void
     */
    public function __construct(Process $process, Credential $actor) {
        $this->process = $process;
        $this->actor = $actor;
    }
}
