<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Company;

use App\Event\AbstractEvent;
use Illuminate\Support\Collection;

/**
 * Deleted event for multiple companies.
 */
class DeletedMulti extends AbstractEvent {
    /**
     * Event related Companies.
     *
     * @var \Illuminate\Support\Collection
     */
    public $companies;

    /**
     * Class constructor.
     *
     * @param \Illuminate\Support\Collection $companies
     *
     * @return void
     */
    public function __construct(Collection $companies) {
        $this->companies = $companies;
    }
}
