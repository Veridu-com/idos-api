<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Profile\Raw;

use App\Entity\Profile\Raw;
use App\Entity\Company\Credential;
use App\Event\AbstractEvent;

/**
 * Deleted event.
 */
class Deleted extends AbstractEvent {
    /**
     * Event related Raw.
     *
     * @var \App\Entity\Profile\Raw
     */
    public $raw;
    /**
     * Event related Credential.
     *
     * @var \App\Entity\Company\Credential
     */
    public $actor;

    /**
     * Class constructor.
     *
     * @param \App\Entity\Profile\Raw $raw
     *
     * @return void
     */
    public function __construct(Raw $raw, Credential $actor) {
        $this->raw = $raw;
        $this->actor = $actor;
    }
}
