<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Tag;

use App\Entity\Tag;
use App\Event\AbstractEvent;

/**
 * Updated event.
 */
class Updated extends AbstractEvent {
    /**
     * Event related Tag.
     *
     * @var App\Entity\Tag
     */
    public $tag;

    /**
     * Class constructor.
     *
     * @param App\Entity\Tag $tag
     *
     * @return void
     */
    public function __construct(Tag $tag) {
        $this->tag = $tag;
    }
}
