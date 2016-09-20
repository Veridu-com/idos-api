<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Profile\Source;

use App\Event\AbstractEvent;
use Illuminate\Support\Collection;

/**
 * Deleted event for multiple sources.
 */
class DeletedMulti extends AbstractEvent {
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
     * @param \Illuminate\Support\Collection $sources
     * @param string                         $ipAddr
     *
     * @return void
     */
    public function __construct(Collection $sources, string $ipAddr) {
        $this->sources = $sources;
        $this->ipAddr  = $ipAddr;
    }
}
