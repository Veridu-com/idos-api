<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Profile\Tag;

use App\Entity\Identity;
use App\Entity\Profile\Tag;
use App\Event\AbstractEvent;

/**
 * Deleted event.
 */
class Deleted extends AbstractEvent {
    /**
     * Event related Tag.
     *
     * @var \App\Entity\Profile\Tag
     */
    public $tag;
    /**
     * Event related Identity.
     *
     * @var \App\Entity\Identity
     */
    public $identity;

    /**
     * Class constructor.
     *
     * @param \App\Entity\Profile\Tag $tag
     * @param \App\Entity\Identity    $identity
     *
     * @return void
     */
    public function __construct(Tag $tag, Identity $identity) {
        $this->tag      = $tag;
        $this->identity = $identity;
    }
}
