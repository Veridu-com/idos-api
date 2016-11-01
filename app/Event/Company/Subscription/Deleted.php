<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Company\Subscription;

use App\Entity\Company\Subscription;
use App\Entity\Identity;
use App\Event\AbstractEvent;

/**
 * Deleted event.
 */
class Deleted extends AbstractEvent {
    /**
     * Event related Subscription.
     *
     * @var \App\Entity\Company\Subscription
     */
    public $subscription;
    /**
     * Event related Identity.
     *
     * @var \App\Entity\Identity
     */
    public $identity;

    /**
     * Class constructor.
     *
     * @param \App\Entity\Company\Subscription $subscription
     *
     * @return void
     */
    public function __construct(Subscription $subscription, Identity $identity) {
        $this->subscription = $subscription;
        $this->identity = $identity;
    }
}
