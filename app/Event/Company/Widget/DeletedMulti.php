<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Company\Widget;

use App\Entity\Company\Widget;
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
    public $actor;

    /**
     * Class constructor.
     *
     * @param \Illuminate\Support\Collection $widgets
     *
     * @return void
     */
    public function __construct(Collection $widgets, Identity $actor) {
        $this->widgets = $widgets;
        $this->actor = $actor;
    }
}
