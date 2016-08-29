<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Attribute;

use App\Entity\Attribute;
use App\Event\AbstractEvent;

/**
 * Created event.
 */
class Created extends AbstractEvent {
    /**
     * Event related Attribute.
     *
     * @var App\Entity\Attribute
     */
    public $attribute;

    /**
     * Class constructor.
     *
     * @param App\Entity\Attribute $attribute
     *
     * @return void
     */
    public function __construct(Attribute $attribute) {
        $this->attribute = $attribute;
    }
}
