<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Event\Company;

use App\Entity\Company;
use App\Event\AbstractEvent;

/**
 * Created event.
 */
class Created extends AbstractEvent {
    public $company;

    public function __construct(Company $company) {
        $this->company = $company;
    }
}
