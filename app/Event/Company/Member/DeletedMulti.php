<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Company\Member;

use App\Event\AbstractEvent;
use Illuminate\Support\Collection;

/**
 * Deleted event for multiple members.
 */
class DeletedMulti extends AbstractEvent {
    /**
     * Event related Members.
     *
     * @var \Illuminate\Support\Collection
     */
    public $members;

    /**
     * Class constructor.
     *
     * @param \Illuminate\Support\Collection $members
     *
     * @return void
     */
    public function __construct(Collection $members) {
        $this->members = $members;
    }
}
