<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event;

use App\Entity\Company\Credential;
use App\Entity\User;

interface ServiceQueueEventInterface {
	/**
	 * Retrieve the "handler" property attribute that will be sent to the Manager.
	 * @return array associative
	 */
    public function getServiceHandlerPayload(array $merge = []) : array;

    /**
     * Retrieves the event related credential.
     * 
     * @return \App\Entity\Company\Credential Credential entity
     */
    public function getCredential() : Credential;

    /**
     * Retrieves the event related user.
     * 
     * @return \App\Entity\User User entity
     */
    public function getUser() : User;

    /**
     * String representation of the event.
     * 
     * @return string Must be the event's id.
     */
    public function __toString();
}