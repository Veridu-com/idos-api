<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Profile\Source;

use App\Entity\Profile\Source;
use App\Entity\User;
use App\Event\AbstractEvent;

/**
 * Deleted event.
 */
class Deleted extends AbstractEvent {
    /**
     * Event related User.
     *
     * @var App\Entity\User
     */
    public $user;
    /**
     * Event related Source.
     *
     * @var App\Entity\Profile\Source
     */
    public $source;
    /**
     * Event related IP Address.
     *
     * @var string
     */
    public $ipAddr;

    /**
     * Class constructor.
     *
     * @param App\Entity\User   $user
     * @param App\Entity\Source $source
     * @param string            $ipAddr
     *
     * @return void
     */
    public function __construct(User $user, Source $source, string $ipAddr) {
        $this->user   = $user;
        $this->source = $source;
        $this->ipAddr = $ipAddr;
    }
}
