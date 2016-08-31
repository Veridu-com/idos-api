<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Credential;

use App\Event\AbstractEvent;
use Illuminate\Support\Collection;

/**
 * Deleted event for multiple credentials.
 */
class DeletedMulti extends AbstractEvent {
    /**
     * Event related Credentials.
     *
     * @var \Illuminate\Support\Collection
     */
    public $credentials;

    /**
     * Class constructor.
     *
     * @param \Illuminate\Support\Collection $credentials
     *
     * @return void
     */
    public function __construct(Collection $credentials) {
        $this->credentials = $credentials;
    }
}
