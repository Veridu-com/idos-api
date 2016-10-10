<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Profile\Tag;

use App\Entity\Profile\Tag;
use App\Event\AbstractEvent;

/**
 * Created event.
 */
class Created extends AbstractEvent {
    /**
     * Event related Tag.
     *
     * @var \App\Entity\Profile\Tag
     */
    public $tag;

    /**
     * Class constructor.
     *
     * @param \App\Entity\Profile\Tag $tag
     *
     * @return void
     */
    public function __construct(Tag $tag) {
        $this->tag = $tag;
    }
}
