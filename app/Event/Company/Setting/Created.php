<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Company\Setting;

use App\Entity\Company;
use App\Entity\Company\Setting;
use App\Entity\Identity;
use App\Event\AbstractEvent;

/**
 * Created event.
 */
class Created extends AbstractEvent {
    /**
     * Event related Setting.
     *
     * @var \App\Entity\Company\Setting
     */
    public $setting;
    /**
     * Event related Identity.
     *
     * @var \App\Entity\Identity
     */
    public $actor;

    /**
     * Class constructor.
     *
     * @param \App\Entity\Company\Setting $setting
     *
     * @return void
     */
    public function __construct(Setting $setting, Identity $actor) {
        $this->setting = $setting;
        $this->actor   = $actor;
    }
}
