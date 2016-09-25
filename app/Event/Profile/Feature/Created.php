<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Profile\Feature;

use App\Entity\Profile\Feature;
use App\Entity\Profile\Source;
use App\Entity\User;
use App\Event\AbstractEvent;
use App\Event\ServiceQueueEventInterface;

/**
 * Created event.
 */
class Created extends AbstractEvent implements ServiceQueueEventInterface {
    /**
     * Event related Feature.
     *
     * @var App\Entity\Profile\Feature
     */
    public $feature;

    /**
     * Event related Source.
     *
     * @var App\Entity\Profile\Source
     */
    public $source;

    /**
     * Event related User.
     *
     * @var App\Entity\User
     */
    public $user;

    /**
     * Class constructor.
     *
     * @param App\Entity\Profile\Feature $feature
     * @param App\Entity\Profile\Source  $source
     * @param App\Entity\User            $user
     *
     * @return void
     */
    public function __construct(Feature $feature, User $user, Credential $credential, Source $source) {
        $this->feature = $feature;
        $this->user = $user;
        $this->source = $source;
        $this->credential = $credential;
    }

    /**
     * Gets the event identifier.
     *
     * @return string 
    **/ 
    public function __toString() {
        // @FIXME double check event identifier
        // does it can have or need $feature->name to be part of it?
        return sprintf('idos:feature.%s.created', $this->source->name);
    }
}
