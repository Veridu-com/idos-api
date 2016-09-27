<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event;

use App\Entity\Company\Credential;
use App\Entity\User;

/**
 * AbstractServiceQueueEvent.
 */
abstract class AbstractServiceQueueEvent extends AbstractEvent implements ServiceQueueEventInterface {
    /**
     * Retrieves the event related credential.
     *
     * @return \App\Entity\Company\Credential Credential entity
     */
    public function getCredential() : Credential {
        return $this->credential;
    }

    /**
     * Sets the event related credential.
     *
     * @param \App\Entity\Company\Credential Credential entity
     *
     * @return void
     */
    public function setCredential(Credential $credential) {
        $this->credential = $credential;
    }

    /**
     * Sets the event related user.
     *
     * @param \App\Entity\User User entity
     *
     * @return void
     */
    public function setUser(User $user) {
        $this->user = $user;
    }

    /**
     * Retrieves the event related user.
     *
     * @return \App\Entity\User User entity
     */
    public function getUser() : User {
        return $this->user;
    }
}
