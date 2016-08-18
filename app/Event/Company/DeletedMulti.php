<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Event\Company;

use App\Event\AbstractEvent;

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
    public function __construct(\Illuminate\Support\Collection $companies) {
        $this->companies = $companies;
    }
}
