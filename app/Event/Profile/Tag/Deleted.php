<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Profile\Tag;

use App\Entity\Profile\Tag;
use App\Entity\Company\Credential;
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
     * Event related Credential.
     *
     * @var \App\Entity\Company\Credential
     */
    public $actor;

    /**
     * Class constructor.
     *
     * @param \App\Entity\Profile\Tag $tag
     *
     * @return void
     */
    public function __construct(Tag $tag, Credential $actor) {
        $this->tag = $tag;
        $this->actor = $actor;
    }
}
