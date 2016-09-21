<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Source;

use App\Entity\User;
use App\Event\AbstractEvent;
use Illuminate\Support\Collection;

/**
 * Deleted event for multiple sources.
 */
class DeletedMulti extends AbstractEvent {
    /**
     * Event related User.
     *
     * @var App\Entity\User
     */
    public $user;
    /**
     * Event related sources.
     *
     * @var \Illuminate\Support\Collection
     */
    public $sources;
    /**
     * Event related IP Address.
     *
     * @var string
     */
    public $ipAddr;

    /**
     * Class constructor.
     *
     * @param App\Entity\User                $user
     * @param \Illuminate\Support\Collection $sources
     * @param string                         $ipAddr
     *
     * @return void
     */
    public function __construct(User $user, Collection $sources, string $ipAddr) {
        $this->user    = $user;
        $this->sources = $sources;
        $this->ipAddr  = $ipAddr;
    }
}
