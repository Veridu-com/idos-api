<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Profile\Feature;

use App\Entity\Profile\Feature;
use App\Entity\Company\Credential;
use App\Event\AbstractEvent;

/**
 * Deleted event.
 */
class Deleted extends AbstractEvent {
    /**
     * Event related Feature.
     *
     * @var \App\Entity\Profile\Feature
     */
    public $feature;
    /**
     * Event related Credential.
     *
     * @var \App\Entity\Company\Credential
     */
    public $credential;

    /**
     * Class constructor.
     *
     * @param \App\Entity\Profile\Feature $feature
     *
     * @return void
     */
    public function __construct(Feature $feature, Credential $credential) {
        $this->feature = $feature;
        $this->credential = $credential;
    }
}
