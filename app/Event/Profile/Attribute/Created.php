<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Profile\Attribute;

use App\Entity\Profile\Attribute;
use App\Entity\Company\Credential;
use App\Event\AbstractEvent;

/**
 * Created event.
 */
class Created extends AbstractEvent {
    /**
     * Event related Attribute.
     *
     * @var \App\Entity\Profile\Attribute
     */
    public $attribute;
    /**
     * Event related Credential.
     *
     * @var \App\Entity\Company\Credential
     */
    public $actor;

    /**
     * Class constructor.
     *
     * @param \App\Entity\Profile\Attribute $attribute
     *
     * @return void
     */
    public function __construct(Attribute $attribute, Credential $actor) {
        $this->attribute = $attribute;
        $this->actor = $actor;
    }
}
