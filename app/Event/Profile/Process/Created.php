<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Profile\Process;

use App\Entity\Company\Credential;
use App\Entity\Profile\Process;
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
    public $credential;

    /**
     * Class constructor.
     *
     * @param \App\Entity\Profile\Process    $process
     * @param \App\Entity\Company\Credential $credential
     *
     * @return void
     */
    public function __construct(Process $process, Credential $credential) {
        $this->process    = $process;
        $this->credential = $credential;
    }
}
