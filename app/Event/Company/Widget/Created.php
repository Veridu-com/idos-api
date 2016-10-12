<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Company\Widget;

use App\Entity\Company\Widget;
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
     * Class constructor.
     *
     * @param \App\Entity\Company\Widget $widget
     *
     * @return void
     */
    public function __construct(Widget $widget) {
        $this->widget = $widget;
    }
}
