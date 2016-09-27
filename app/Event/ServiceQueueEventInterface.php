<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event;

interface ServiceQueueEventInterface {
    /**
     * Retrieve the "handler" property attribute that will be sent to the Manager.
     *
     * @return array associative
     */
    public function getServiceHandlerPayload(array $merge = []) : array;

    /**
     * String representation of the event.
     *
     * @return string Must be the event's id.
     */
    public function __toString();
}
