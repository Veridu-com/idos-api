<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Credential;

use App\Entity\Credential;
use App\Event\AbstractEvent;

/**
 * Deleted event.
 */
class Deleted extends AbstractEvent {
    /**
     * Event related Credential.
     *
     * @var App\Entity\Credential
     */
    public $credential;

    /**
     * Class constructor.
     *
     * @param App\Entity\Credential $credential
     *
     * @return void
     */
    public function __construct(Credential $credential) {
        $this->credential = $credential;
    }
}
