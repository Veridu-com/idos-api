<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Profile\Raw;

use App\Entity\Company\Credential;
use App\Event\AbstractEvent;
use Illuminate\Support\Collection;

/**
 * Deleted event for multiple raw data.
 */
class DeletedMulti extends AbstractEvent {
    /**
     * Event related Raw.
     *
     * @var \Illuminate\Support\Collection
     */
    public $raw;
    /**
     * Event related Credential.
     *
     * @var \App\Entity\Company\Credential
     */
    public $actor;

    /**
     * Class constructor.
     *
     * @param \Illuminate\Support\Collection $raws
     *
     * @return void
     */
    public function __construct(Collection $raw, Credential $actor) {
        $this->raw = $raw;
        $this->actor = $actor;
    }
}
