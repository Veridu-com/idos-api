<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Profile\Gate;

use App\Entity\Company\Credential;
use App\Event\AbstractEvent;
use Illuminate\Support\Collection;

/**
 * Deleted event for multiple gates.
 */
class DeletedMulti extends AbstractEvent {
    /**
     * Event related Gates.
     *
     * @var \Illuminate\Support\Collection
     */
    public $gates;
    /**
     * Event related Credential.
     *
     * @var \App\Entity\Company\Credential
     */
    public $credential;

    /**
     * Class constructor.
     *
     * @param \Illuminate\Support\Collection $gates
     * @param \App\Entity\Company\Credential $credential
     *
     * @return void
     */
    public function __construct(Collection $gates, Credential $credential) {
        $this->gates = $gates;
        $this->credential = $credential;
    }
}
