<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Profile\Feature;

use App\Entity\Company\Credential;
use App\Event\AbstractEvent;
use Illuminate\Support\Collection;

/**
 * Deleted event for multiple features.
 */
class DeletedMulti extends AbstractEvent {
    /**
     * Event related Features.
     *
     * @var \Illuminate\Support\Collection
     */
    public $features;
    /**
     * Event related Credential.
     *
     * @var \App\Entity\Company\Credential
     */
    public $credential;

    /**
     * Class constructor.
     *
     * @param \Illuminate\Support\Collection $features
     *
     * @return void
     */
    public function __construct(Collection $features, Credential $credential) {
        $this->features = $features;
        $this->credential = $credential;
    }
}
