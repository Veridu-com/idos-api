<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Reference;

use App\Entity\Reference;
use App\Event\AbstractEvent;

/**
 * Updated event.
 */
class Updated extends AbstractEvent {
    /**
     * Event related Reference.
     *
     * @var App\Entity\Reference
     */
    public $reference;

    /**
     * Class constructor.
     *
     * @param App\Entity\Reference $reference
     *
     * @return void
     */
    public function __construct(Reference $reference) {
        $this->reference = $reference;
    }
}
