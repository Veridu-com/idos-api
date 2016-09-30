<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Identity;

use App\Entity\Identity;
use App\Event\AbstractEvent;

/**
 * Created event.
 */
class Created extends AbstractEvent {
    /**
     * Event related Identity.
     *
     * @var App\Entity\Identity
     */
    public $identity;

    /**
     * Class constructor.
     *
     * @param App\Entity\Identity $identity
     *
     * @return void
     */
    public function __construct(Identity $identity) {
        $this->identity = $identity;
    }
}
