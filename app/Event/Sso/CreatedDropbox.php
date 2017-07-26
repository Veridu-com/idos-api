<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Sso;

use App\Event\AbstractEvent;

/**
 * Created event.
 */
class CreatedDropbox extends AbstractEvent {
    /**
     * Event related username.
     *
     * @var string
     */
    public $username;

    /**
     * Class constructor.
     *
     * @param string $username
     *
     * @return void
     */
    public function __construct(string $username) {
        $this->username = $username;
    }
}
