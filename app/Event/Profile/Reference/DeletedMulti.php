<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Profile\Reference;

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
     * Class constructor.
     *
     * @param \Illuminate\Support\Collection $references
     *
     * @return void
     */
    public function __construct(Collection $references) {
        $this->references = $references;
    }
}
