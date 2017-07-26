<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Company\Setting;

use App\Entity\Company;
use App\Entity\Company\Setting;
use App\Entity\Identity;
use App\Event\AbstractEvent;

/**
 * Deleted event.
 */
class Deleted extends AbstractEvent {
    /**
     * Event related Setting.
     *
     * @var \App\Entity\Company\Setting
     */
    public $setting;
    /**
     * Event related Company.
     *
     * @var \App\Entity\Company
     */
    public $company;
    /**
     * Event related Identity.
     *
     * @var \App\Entity\Identity
     */
    public $identity;

    /**
     * Class constructor.
     *
     * @param \App\Entity\Company\Setting $setting
     * @param \App\Entity\Company         $company
     * @param \App\Entity\Identity        $identity
     *
     * @return void
     */
    public function __construct(Setting $setting, Company $company, Identity $identity) {
        $this->setting  = $setting;
        $this->company  = $company;
        $this->identity = $identity;
    }
}
