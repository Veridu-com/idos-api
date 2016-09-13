<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Source;

use App\Entity\Source;
use App\Event\AbstractEvent;

/**
 * Updated event.
 */
class Updated extends AbstractEvent {
    /**
     * Event related Source.
     *
     * @var App\Entity\Source
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
     * @param App\Entity\Source $source
     * @param string            $ipAddr
     *
     * @return void
     */
    public function __construct(Source $source, string $ipAddr) {
        $this->source = $source;
        $this->ipAddr = $ipAddr;
    }
}
