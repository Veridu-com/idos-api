<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Credential;

use App\Entity\Credential;
use App\Entity\Identity;
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
     * Event related Identity.
     *
     * @var App\Entity\Identity
     */
    public $identity;

    /**
     * Class constructor.
     *
     * @param App\Entity\Credential $credential
     * @param App\Entity\Identity   $identity
     *
     * @return void
     */
    public function __construct(Credential $credential, Identity $identity) {
        $this->credential = $credential;
        $this->identity   = $identity;
    }
}
