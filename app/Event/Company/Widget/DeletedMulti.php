<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Company\Widget;

use App\Entity\Identity;
use App\Event\AbstractEvent;
use Illuminate\Support\Collection;

/**
 * DeletedMulti event.
 */
class DeletedMulti extends AbstractEvent {
    /**
     * Event related Widget.
     *
     * @var \Illuminate\Support\Collection
     */
    public $widgets;
    /**
     * Event related Identity.
     *
     * @var \App\Entity\Identity
     */
    public $identity;

    /**
     * Class constructor.
     *
     * @param \Illuminate\Support\Collection $widgets
     * @param \App\Entity\Identity           $identity
     *
     * @return void
     */
    public function __construct(Collection $widgets, Identity $identity) {
        $this->widgets  = $widgets;
        $this->identity = $identity;
    }
}
