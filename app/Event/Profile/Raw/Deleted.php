<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Profile\Raw;

use App\Entity\Company\Credential;
use App\Entity\Profile\Raw;
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
    public $credential;

    /**
     * Class constructor.
     *
     * @param \App\Entity\Profile\Raw        $raw
     * @param \App\Entity\Company\Credential $credential
     *
     * @return void
     */
    public function __construct(Raw $raw, Credential $credential) {
        $this->raw        = $raw;
        $this->credential = $credential;
    }
}
