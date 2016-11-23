<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Company\Member;

use App\Entity\Identity;
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
     * Event related Identity.
     *
     * @var \App\Entity\Identity
     */
    public $identity;

    /**
     * Class constructor.
     *
     * @param \Illuminate\Support\Collection $members
     * @param \App\Entity\Identity           $identity
     *
     * @return void
     */
    public function __construct(Collection $members, Identity $identity) {
        $this->members  = $members;
        $this->identity = $identity;
    }
}
