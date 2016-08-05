<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Event\Company;

use App\Entity\Company;
use App\Event\AbstractEvent;

/**
 * Created event.
 */
class Created extends AbstractEvent {
    /**
     * Event related Company.
     *
     * @var App\Entity\Company
     */
    public $company;

    /**
     * Class constructor.
     *
     * @param App\Entity\Company $company
     *
     * @return void
     */
    public function __construct(Company $company) {
        $this->company = $company;
    }
}
