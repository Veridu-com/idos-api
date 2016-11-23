<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Profile\Source;

use App\Entity\Company\Credential;
use App\Entity\Profile\Source;
use App\Event\AbstractEvent;

/**
 * CRA event.
 */
class CRA extends AbstractEvent {
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
     * @param \App\Entity\Profile\Source     $source
     * @param string                         $ipAddr
     * @param \App\Entity\Company\Credential $credential
     *
     * @return void
     */
    public function __construct(Source $source, string $ipAddr, Credential $credential) {
        $this->source     = $source;
        $this->ipAddr     = $ipAddr;
        $this->credential = $credential;
    }
}
