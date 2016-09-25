<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Profile\Task;

use App\Entity\Company\Credential;
use App\Entity\Profile\Task;
use App\Entity\Service;
use App\Entity\User;
use App\Event\AbstractEvent;
use App\Event\ServiceQueueEventInterface;
use App\Listener\QueueCompanyServiceHandlers;

/**
 * Completed event.
 */
class Completed extends AbstractEvent implements ServiceQueueEventInterface {
    use QueueCompanyServiceHandlers;

    /**
     * Event related Task.
     *
     * @var App\Entity\Profile\Task
     */
    public $task;

    /**
     * Event related User.
     *
     * @var App\Entity\User
     */
    public $user;

    /**
     * Event related Credential.
     *
     * @var App\Entity\Company\Credential
     */
    public $credential;

    /**
     * Class constructor.
     *
     * @param App\Entity\Profile\Task $task
     *
     * @return void
     */
    /**
     * Class contructor.
     * 
     * @param Task       $task            Task completed
     * @param User       $user            Target User
     * @param Credential $credential      Target Credential
     * @param string     $eventIdentifier Event identifier  eg.: "idos:scraper.facebook.completed" (comes from the Task "creator property")
     */
    public function __construct(Task $task, User $user, Credential $credential, string $eventIdentifier) {
        $this->task = $task;
        $this->user = $user;
        $this->credential = $credential;
        $this->eventIdentifier = $eventIdentifier;
    }

    /**
     * {inheritdoc}
     */
    public function getServiceHandlerPayload(array $merge = []) : array {
        return array_merge([], $merge);
    }

    /**
     * {inheritdoc}
     */
    public function getUser() : User {
        return $this->user;
    }
    /**
     * {inheritdoc}
     */
    public function getCredential() : Credential {
        return $this->credential;
    }
    /**
     * {inheritdoc}
     */
    public function __toString() {
        return $this->eventIdentifier;
    }
}
