<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Profile\Tag;

use App\Entity\Identity;
use App\Event\AbstractEvent;
use Illuminate\Support\Collection;

/**
 * Deleted event for multiple tag.
 */
class DeletedMulti extends AbstractEvent {
    /**
     * Event related Tags.
     *
     * @var \Illuminate\Support\Collection
     */
    public $tags;
    /**
     * Event related Identity.
     *
     * @var \App\Entity\Identity
     */
    public $identity;

    /**
     * Class constructor.
     *
     * @param \Illuminate\Support\Collection $tags
     * @param \App\Entity\Identity           $identity
     *
     * @return void
     */
    public function __construct(Collection $tags, Identity $identity) {
        $this->tags     = $tags;
        $this->identity = $identity;
    }
}
