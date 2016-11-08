<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Profile\Attribute;

use App\Entity\Profile\Attribute;
use App\Event\AbstractEvent;

/**
 * Deleted event.
 */
class Deleted extends AbstractEvent {
    /**
     * Event related Attribute.
     *
     * @var \App\Entity\Profile\Attribute
     */
    public $attribute;

    /**
     * Class constructor.
     *
     * @param \App\Entity\Profile\Attribute $attribute
     *
     * @return void
     */
    public function __construct(Attribute $attribute) {
        $this->attribute = $attribute;
    }
}
