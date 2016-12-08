<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Profile\Gate;

use App\Entity\Company\Credential;
use App\Entity\Profile\Gate;
use App\Event\AbstractEvent;
use App\Event\Interfaces\UserIdGetterInterface;

/**
 * Updated event.
 */
class Updated extends AbstractEvent implements UserIdGetterInterface {
    /**
     * Event related Gate.
     *
     * @var \App\Entity\Profile\Gate
     */
    public $gate;
    /**
     * Event related Credential.
     *
     * @var \App\Entity\Company\Credential
     */
    public $credential;

    /**
     * Class constructor.
     *
     * @param \App\Entity\Profile\Gate       $gate
     * @param \App\Entity\Company\Credential $credential
     *
     * @return void
     */
    public function __construct(Gate $gate, Credential $credential) {
        $this->gate       = $gate;
        $this->credential = $credential;
    }

    /**
     * {@inheritdoc}
     */
    public function getUserId(): int {
        return $this->gate->userId;
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string String representation of the object.
     */
    public function __toString() {
        return sprintf('gate.updated.%s', $this->gate->slug);
    }
}
