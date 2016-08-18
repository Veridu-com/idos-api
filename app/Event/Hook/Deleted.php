<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Event\Hook;

use App\Entity\Hook;
use App\Event\AbstractEvent;

/**
 * Deleted event.
 */
class Deleted extends AbstractEvent {
    /**
     * Event related Hook.
     *
     * @var int
     */
    public $result;

    /**
     * Class constructor.
     *
     * @param int $result
     *
     * @return void
     */
    public function __construct(int $result) {
        $this->result = $result;
    }
}
