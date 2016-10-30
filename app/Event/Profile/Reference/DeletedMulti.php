<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Profile\Reference;

use App\Entity\Company\Credential;
use App\Event\AbstractEvent;
use Illuminate\Support\Collection;

/**
 * Deleted event for multiple references.
 */
class DeletedMulti extends AbstractEvent {
    /**
     * Event related References.
     *
     * @var \Illuminate\Support\Collection
     */
    public $references;
    /**
     * Event related Credential.
     *
     * @var \App\Entity\Company\Credential
     */
    public $actor;

    /**
     * Class constructor.
     *
     * @param \Illuminate\Support\Collection $references
     *
     * @return void
     */
    public function __construct(Collection $references, Credential $actor) {
        $this->references = $references;
        $this->actor = $actor;
    }
}
