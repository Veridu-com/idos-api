<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Profile\Flag;

use App\Entity\Profile\Flag;
use App\Event\AbstractEvent;

/**
 * Deleted event.
 */
class Deleted extends AbstractEvent {
    /**
     * Event related Flag.
     *
     * @var \App\Entity\Profile\Flag
     */
    public $flag;

    /**
     * Class constructor.
     *
     * @param \App\Entity\Profile\Flag $flag
     *
     * @return void
     */
    public function __construct(Flag $flag) {
        $this->flag = $flag;
    }
}
