<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Profile\Task;

use App\Entity\Company\Credential;
use App\Entity\Profile\Task;
use App\Entity\User;
use App\Event\AbstractServiceQueueEvent;

/**
 * Completed event.
 */
class Completed extends AbstractServiceQueueEvent {
    /**
     * Event related Task.
     *
     * @var \App\Entity\Profile\Task
     */
    public $task;
    /**
     * Event related User.
     *
     * @var \App\Entity\User
     */
    public $user;
    /**
     * Event related Credential.
     *
     * @var \App\Entity\Company\Credential
     */
    public $credential;

    /**
     * Class contructor.
     *
     * @param \App\Entity\Profile\Task       $task            Task completed
     * @param \App\Entity\User               $user            Target User
     * @param string                         $eventIdentifier Event identifier  eg.: "idos:scraper.facebook.completed" (comes from the Task "creator property")
     * @param \App\Entity\Company\Credential $credential      Target Credential
     */
    public function __construct(Task $task, User $user, string $eventIdentifier, Credential $credential) {
        $this->task            = $task;
        $this->user            = $user;
        $this->eventIdentifier = $eventIdentifier;
        $this->credential      = $credential;
    }

    /**
     * {inheritdoc}.
     */
    public function getServiceHandlerPayload(array $merge = []) : array {
        return array_merge([], $merge);
    }

    /**
     * {inheritdoc}.
     */
    public function __toString() {
        return $this->eventIdentifier;
    }
}
