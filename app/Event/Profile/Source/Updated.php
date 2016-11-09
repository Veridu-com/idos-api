<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Profile\Source;

use App\Entity\Profile\Source;
use App\Entity\User;
use App\Entity\Company\Credential;
use App\Event\AbstractEvent;

/**
 * Updated event.
 */
class Updated extends AbstractEvent {
    /**
     * Event related User.
     *
     * @var \App\Entity\User
     */
    public $user;
    /**
     * Event related Source.
     *
     * @var \App\Entity\Profile\Source
     */
    public $source;
    /**
     * Event related IP Address.
     *
     * @var string
     */
    public $ipAddr;
    /**
     * Event related Credential.
     *
     * @var \App\Entity\Company\Credential
     */
    public $credential;

    /**
     * Class constructor.
     *
     * @param \App\Entity\Profile\Source $source
     * @param string                     $ipAddr
     * @param \App\Entity\Company\Credential $credential
     *
     * @return void
     */
    public function __construct(User $user, Source $source, string $ipAddr, Credential $credential) {
        $this->user   = $user;
        $this->source = $source;
        $this->ipAddr = $ipAddr;
        $this->credential = $credential;
    }
}
