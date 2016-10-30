<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Company\Credential;

use App\Entity\Company\Credential;
use App\Entity\Identity;
use App\Event\AbstractEvent;

/**
 * Created event.
 */
class Created extends AbstractEvent {
    /**
     * Event related Credential.
     *
     * @var \App\Entity\Company\Credential
     */
    public $credential;
    /**
     * Event related Identity.
     *
     * @var \App\Entity\Identity
     */
    public $actor;

    /**
     * Class constructor.
     *
     * @param \App\Entity\Company\Credential $credential
     * @param \App\Entity\Identity           $actor
     *
     * @return void
     */
    public function __construct(Credential $credential, Identity $actor) {
        $this->credential = $credential;
        $this->actor      = $actor;
    }
}
