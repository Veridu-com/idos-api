<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Profile\Tag;

use App\Entity\Company\Credential;
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
     * Event related Credential.
     *
     * @var \App\Entity\Company\Credential
     */
    public $actor;

    /**
     * Class constructor.
     *
     * @param \Illuminate\Support\Collection $tags
     *
     * @return void
     */
    public function __construct(Collection $tags, Credential $actor) {
        $this->tags = $tags;
        $this->actor = $actor;
    }
}
