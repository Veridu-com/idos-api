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

/**
 * Deleted event.
 */
class Deleted extends AbstractEvent {
    /**
     * Event related Widget.
     *
     * @var \App\Entity\Company\Widget
     */
    public $widget;

    /**
     * Event related Identity.
     *
     * @var \App\Entity\Company\Identity
     */
    public $actor;

    /**
     * Class constructor.
     *
     * @param \App\Entity\Company\Widget $widget
     * @param \App\Entity\Identity       $actor
     *
     * @return void
     */
    public function __construct(Widget $widget, Identity $actor) {
        $this->widget = $widget;
        $this->actor  = $actor;
    }
}