<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Profile\Task;

use App\Entity\Profile\Task;
use App\Event\AbstractEvent;

/**
 * Created event.
 */
class Created extends AbstractEvent {
    /**
     * Event related Task.
     *
     * @var \App\Entity\Profile\Task
     */
    public $task;

    /**
     * Class constructor.
     *
     * @param \App\Entity\Profile\Task $task
     *
     * @return void
     */
    public function __construct(Task $task) {
        $this->task = $task;
    }
}
