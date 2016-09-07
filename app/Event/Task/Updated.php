<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Task;

use App\Entity\Task;
use App\Event\AbstractEvent;

/**
 * Updated event.
 */
class Updated extends AbstractEvent {
    /**
     * Event related Task.
     *
     * @var App\Entity\Task
     */
    public $task;

    /**
     * Class constructor.
     *
     * @param App\Entity\Task $task
     *
     * @return void
     */
    public function __construct(Task $task) {
        $this->task = $task;
    }
}
