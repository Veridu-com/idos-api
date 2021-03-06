<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Company\Widget;

use App\Entity\Company\Widget;
use App\Entity\Identity;
use App\Event\AbstractEvent;

/**
 * Created event.
 */
class Created extends AbstractEvent {
    /**
     * Event related Widget.
     *
     * @var \App\Entity\Company\Widget
     */
    public $widget;
    /**
     * Event related Identity.
     *
     * @var \App\Entity\Identity
     */
    public $identity;

    /**
     * Class constructor.
     *
     * @param \App\Entity\Company\Widget $widget
     * @param \App\Entity\Identity       $identity
     *
     * @return void
     */
    public function __construct(Widget $widget, Identity $identity) {
        $this->widget   = $widget;
        $this->identity = $identity;
    }
}
