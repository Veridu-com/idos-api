<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Profile\Reference;

use App\Entity\Profile\Reference;
use App\Entity\Company\Credential;
use App\Event\AbstractEvent;

/**
 * Created event.
 */
class Created extends AbstractEvent {
    /**
     * Event related Reference.
     *
     * @var \App\Entity\Profile\Reference
     */
    public $reference;
    /**
     * Event related Credential.
     *
     * @var \App\Entity\Company\Credential
     */
    public $actor;

    /**
     * Class constructor.
     *
     * @param \App\Entity\Profile\Reference $reference
     *
     * @return void
     */
    public function __construct(Reference $reference, Credential $actor) {
        $this->reference = $reference;
        $this->actor = $actor;
    }
}
