<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Company\Hook;

use App\Entity\Company\Hook;
use App\Entity\Identity;
use App\Event\AbstractEvent;
use Illuminate\Support\Collection;

/**
 * DeletedMulti event.
 */
class DeletedMulti extends AbstractEvent {
    /**
     * Event related Hook.
     *
     * @var \Illuminate\Support\Collection
     */
    public $hooks;
    /**
     * Event related Identity.
     *
     * @var \App\Entity\Identity
     */
    public $identity;

    /**
     * Class constructor.
     *
     * @param \Illuminate\Support\Collection $hooks
     * @param \App\Entity\Identity           $identity
     *
     * @return void
     */
    public function __construct(Collection $hooks, Identity $identity) {
        $this->hooks    = $hooks;
        $this->identity = $identity;
    }
}
