<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Hook;

use App\Entity\Hook;
use App\Event\AbstractEvent;
use Illuminate\Support\Collection;

/**
 * DeletedMulti event.
 */
class DeletedMulti extends AbstractEvent {
    /**
     * Event related Hook.
     *
     * @var Illuminate\Support\Collection
     */
    public $hooks;

    /**
     * Class constructor.
     *
     * @param Illuminate\Support\Collection $hooks
     *
     * @return void
     */
    public function __construct(Collection $hooks) {
        $this->hooks = $hooks;
    }
}
