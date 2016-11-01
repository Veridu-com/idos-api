<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Company;

use App\Entity\Company;
use App\Entity\Identity;
use App\Event\AbstractEvent;

/**
 * Updated event.
 */
class Updated extends AbstractEvent {
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
     * @param \App\Entity\Company  $company
     * @param \App\Entity\Identity $identity
     *
     * @return void
     */
    public function __construct(Company $company, Identity $identity) {
        $this->company  = $company;
        $this->identity = $identity;
    }
}
